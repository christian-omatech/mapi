<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Support\Str;
use Omatech\Mapi\Editora\Infrastructure\Instance\StructureLoaderInterface;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;

final class InstanceRepository implements InstanceRepositoryInterface
{
    private StructureLoaderInterface $structureLoader;

    public function __construct(StructureLoaderInterface $structureLoader)
    {
        $this->structureLoader = $structureLoader;
    }

    public function build(string $classKey): Instance
    {
        $classKey = Str::ucfirst(Str::camel($classKey));
        return (new InstanceBuilder())
            ->setLanguages(config('mage.editora.languages'))
            ->setStructure($this->structureLoader->load($classKey))
            ->setClassName($classKey)
            ->build();
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
    public function classKey(int $id): ?string
    {
    }
    public function delete(Instance $instance): void
    {
        InstanceDAO::find($instance->toArray()['metadata']['id'])
            ->forceDelete();
    }

    public function save(Instance $instance): void
    {
        $model = InstanceDAO::updateOrCreate([
            'id' => $instance->toArray()['metadata']['id'],
        ], $this->toDB($instance));

        $model->values()->saveMany(
            $this->parseAttributes($instance->toArray()['attributes'])
        );
    }

    private function toDB(Instance $instance): array
    {
        $data = $instance->toArray();
        return [
            'class_key' => $data['class']['key'],
            'key' => $data['metadata']['key'],
            'status' => $data['metadata']['publication']['status'],
            'start_publishing_date' => $data['metadata']['publication']['startPublishingDate'],
        ];
    }

    private function parseAttributes(array $attributes): array
    {
        $parsedAttributes = [];
        foreach ($attributes as $attribute) {
            foreach ($attribute['values'] as $value) {
                $parsedAttributes[] = ValueDAO::firstOrNew([
                    'id' => $value['id'],
                ], [
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
