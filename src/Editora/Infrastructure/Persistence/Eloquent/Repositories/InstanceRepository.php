<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories;

use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\InstanceBuilder;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;

final class InstanceRepository implements InstanceRepositoryInterface
{
    private InstanceBuilder $instanceBuilder;

    public function __construct(InstanceBuilder $instanceBuilder)
    {
        $this->instanceBuilder = $instanceBuilder;
    }

    public function build(string $classKey): Instance
    {
        return $this->instanceBuilder->build($classKey);
    }

    public function find(int $id): ?Instance
    {
        $model = InstanceDAO::find($id);
        if (is_null($model)) {
            return null;
        }
        $instance = $this->build($model->class_key);
        $instance->fill($this->fromDB($model));
        return $instance;
    }

    public function exists(string $key): bool
    {
        return InstanceDAO::where('key', $key)->exists();
    }

    public function classKey(int $id): ?string
    {
    }

    public function delete(Instance $instance): void
    {
        InstanceDAO::find($instance->id())->forceDelete();
    }

    public function save(Instance $instance): void
    {
        $model = InstanceDAO::updateOrCreate([
            'id' => $instance->id(),
        ], $this->toDB($instance));
        $model->values()->saveMany(
            $this->parseAttributes($instance->attributes())
        );
        $instance->fill($this->fromDB($model));
    }

    private function toDB(Instance $instance): array
    {
        return [
            'class_key' => $instance->data()['classKey'],
            'key' => $instance->data()['key'],
            'status' => $instance->data()['status'],
            'start_publishing_date' => $instance->data()['startPublishingDate']
                ->format('Y-m-d H:i:s'),
            'end_publishing_date' => $instance->data()['endPublishingDate']
                ?->format('Y-m-d H:i:s'),
        ];
    }

    private function parseAttributes(array $attributes): array
    {
        $parsedAttributes = [];
        foreach ($attributes as $attribute) {
            foreach ($attribute['values'] as $value) {
                $parsedAttributes[] = ValueDAO::firstOrNew([
                    'id' => $value['id'],
                ])->fill([
                    'attribute_key' => $attribute['key'],
                    'value' => $value['value'],
                    'language' => $value['language'],
                ]);
            }
        }
        return $parsedAttributes;
    }

    private function fromDB(InstanceDAO $instanceDAO): array
    {
        $attributes = [];
        foreach ($instanceDAO->values()->get() as $value) {
            $attributes[$value->attribute_key]['values'][] = [
                'id' => $value->id,
                'language' => $value->language,
                'value' => $value->value,
            ];
        }
        return [
            'metadata' => [
                'key' => $instanceDAO->key,
                'id' => $instanceDAO->id,
                'publication' => [
                    'status' => $instanceDAO->status,
                    'startPublishingDate' => $instanceDAO->start_publishing_date,
                    'endPublishingDate' => $instanceDAO->end_publishing_date,
                ],
            ],
            'attributes' => $attributes,
            'relations' => [],
        ];
    }
}
