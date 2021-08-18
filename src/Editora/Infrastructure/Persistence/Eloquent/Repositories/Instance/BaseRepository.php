<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Illuminate\Support\Collection;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\InstanceBuilder;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;

abstract class BaseRepository
{
    protected InstanceDAO $instance;
    protected AttributeDAO $attribute;
    protected ValueDAO $value;
    protected InstanceBuilder $instanceBuilder;

    public function __construct(InstanceBuilder $instanceBuilder)
    {
        $this->instance = new InstanceDAO();
        $this->attribute = new AttributeDAO();
        $this->value = new ValueDAO();
        $this->instanceBuilder = $instanceBuilder;
    }

    protected function instanceToDB(Instance $instance): void
    {
        $instanceDAO = $this->instance->updateOrCreate([
            'id' => $instance->id(),
        ], [
            'class_key' => $instance->data()['classKey'],
            'key' => $instance->data()['key'],
            'status' => $instance->data()['status'],
            'start_publishing_date' => $instance->data()['startPublishingDate']
                ->format('Y-m-d H:i:s'),
            'end_publishing_date' => $instance->data()['endPublishingDate']
                ?->format('Y-m-d H:i:s'),
        ]);
        $this->attributesToDB($instance->attributes(), $instanceDAO);
        $instance->fill($this->instanceFromDB($instanceDAO));
    }

    private function attributesToDB(
        array $attributes,
        InstanceDAO $instanceDAO,
        ?int $parentAttributeId = null
    ): void {
        each(function (array $attribute) use ($instanceDAO, $parentAttributeId) {
            $attributeDAO = $this->attribute->updateOrCreate([
                'instance_id' => $instanceDAO->id,
                'parent_id' => $parentAttributeId,
                'key' => $attribute['key'],
            ], [
                'attribute_key' => $attribute['key'],
            ]);
            $this->valuesToDB($attribute['values'], $attributeDAO);
            $this->attributesToDB($attribute['attributes'], $instanceDAO, $attributeDAO->id);
        }, $attributes);
    }

    private function valuesToDB(array $values, AttributeDAO $attributeDAO): void
    {
        each(function (array $value) use ($attributeDAO) {
            $this->value->updateOrCreate([
                'attribute_id' => $attributeDAO->id,
                'language' => $value['language'],
            ], [
                'language' => $value['language'],
                'value' => $value['value'],
            ]);
        }, $values);
    }

    protected function instanceFromDB(InstanceDAO $instanceDAO): array
    {
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
            'attributes' => $this->attributesFromDB($instanceDAO->attributes),
            'relations' => [],
        ];
    }

    private function attributesFromDB(Collection $attributes): array
    {
        $attributes = $this->attributesToTree($attributes);
        return $this->parseAttributes($attributes);
    }

    private function attributesToTree(Collection $attributes, $parentId = null): array
    {
        $treeAttributes = [];
        each(function (AttributeDAO $attribute) use ($attributes, $parentId, &$treeAttributes) {
            if ($attribute->parent_id === $parentId) {
                $children = $this->attributesToTree($attributes, $attribute->id);
                if ($children) {
                    $attribute['attributes'] = $children;
                }
                $treeAttributes[$attribute->id] = $attribute;
                unset($attributes[$attribute->id]);
            }
        }, $attributes);
        return $treeAttributes;
    }

    private function parseAttributes(array $attributes): array
    {
        $parsedAttributes = [];
        each(function ($attribute) use (&$parsedAttributes) {
            if ($attribute->attributes) {
                $parsedAttributes[$attribute->key]['attributes'] =
                    $this->parseAttributes($attribute->attributes);
            }
            $parsedAttributes[$attribute->key]['values'] = $this->valuesFromDB($attribute->values);
        }, $attributes);
        return $parsedAttributes;
    }

    private function valuesFromDB(Collection $values): array
    {
        return map(function (ValueDAO $value): array {
            return [
                'id' => $value->id,
                'language' => $value->language,
                'value' => $value->value,
            ];
        }, $values);
    }
}
