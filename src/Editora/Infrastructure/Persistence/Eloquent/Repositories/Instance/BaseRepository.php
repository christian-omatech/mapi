<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Illuminate\Support\Collection;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\InstanceBuilder;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\RelationDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Omatech\Mcore\Editora\Domain\Attribute\Attribute;
use Omatech\Mcore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceRelation;
use Omatech\Mcore\Editora\Domain\Instance\InstanceRelationCollection;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;
use Omatech\Mcore\Editora\Domain\Value\ValueCollection;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

abstract class BaseRepository
{
    protected InstanceDAO $instance;
    protected AttributeDAO $attribute;
    protected ValueDAO $value;
    protected RelationDAO $relation;
    protected InstanceBuilder $instanceBuilder;

    public function __construct(InstanceBuilder $instanceBuilder)
    {
        $this->instance = new InstanceDAO();
        $this->attribute = new AttributeDAO();
        $this->value = new ValueDAO();
        $this->relation = new RelationDAO();
        $this->instanceBuilder = $instanceBuilder;
    }

    protected function instanceToDB(Instance $instance): void
    {
        $instanceDAO = $this->instance->updateOrCreate([
            'id' => $instance->id(),
            'class_key' => $instance->data()['classKey'],
        ], [
            'key' => $instance->data()['key'],
            'status' => $instance->data()['status'],
            'start_publishing_date' => $instance->data()['startPublishingDate']
                ->format('Y-m-d H:i:s'),
            'end_publishing_date' => $instance->data()['endPublishingDate']
                ?->format('Y-m-d H:i:s'),
        ]);
        $this->attributesToDB($instance->attributes(), $instanceDAO);
        $this->relationsToDB($instance->relations(), $instanceDAO);
        $instance->fill($this->instanceFromDB($instanceDAO));
    }

    private function attributesToDB(
        AttributeCollection $attributes,
        InstanceDAO $instanceDAO,
        ?int $parentAttributeId = null
    ): void {
        each(function (Attribute $attribute) use ($instanceDAO, $parentAttributeId) {
            $attributeDAO = $this->attribute->updateOrCreate([
                'instance_id' => $instanceDAO->id,
                'parent_id' => $parentAttributeId,
                'key' => $attribute->key(),
            ]);
            $this->valuesToDB($attribute->values(), $attributeDAO);
            $this->attributesToDB($attribute->attributes(), $instanceDAO, $attributeDAO->id);
        }, $attributes->get());
    }

    private function valuesToDB(ValueCollection $values, AttributeDAO $attributeDAO): void
    {
        each(function (BaseValue $value) use ($attributeDAO) {
            $this->value->updateOrCreate([
                'id' => $value->id(),
                'attribute_id' => $attributeDAO->id,
                'language' => $value->language(),
            ], [
                'value' => $value->value(),
                'extra_data' => json_encode($value->extraData()),
            ]);
        }, $values->get());
    }

    private function relationsToDB(
        InstanceRelationCollection $relations,
        InstanceDAO $instanceDAO
    ): void {
        each(function (InstanceRelation $relation) use ($instanceDAO) {
            each(function (int $instanceId, int $index) use ($relation, $instanceDAO) {
                $this->relation->updateOrCreate([
                    'key' => $relation->key(),
                    'parent_instance_id' => $instanceDAO->id,
                    'child_instance_id' => $instanceId,
                ], [
                    'order' => $index,
                ]);
            }, array_keys($relation->instances()), $instanceDAO->id);
        }, $relations->get());
        $this->deleteRelations($instanceDAO, $relations);
    }

    private function deleteRelations(
        InstanceDAO $instanceDAO,
        InstanceRelationCollection $expectedRelations
    ): void {
        $dbRelations = $instanceDAO->relations->filter(
            function (RelationDAO $relation) use ($expectedRelations) {
                $expectedRelation = search(
                    function (InstanceRelation $expectedRelation) use ($relation) {
                        return $relation->key === $expectedRelation->key();
                    },
                    $expectedRelations->get()
                );
                return ! $expectedRelation?->instanceExists($relation->child_instance_id);
            }
        );
        $dbRelations->each(fn (RelationDAO $relation) => $relation->forceDelete());
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
            'relations' => $this->relationsFromDB($instanceDAO->relations),
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
        return reduce(function (array $acc, AttributeDAO $attribute): array {
            if ($attribute->attributes) {
                $acc[$attribute->key]['attributes'] =
                    $this->parseAttributes($attribute->attributes);
            }
            $acc[$attribute->key]['values'] = $this->valuesFromDB($attribute->values);
            return $acc;
        }, $attributes, []);
    }

    private function valuesFromDB(Collection $values): array
    {
        return map(function (ValueDAO $value): array {
            return [
                'id' => $value->id,
                'language' => $value->language,
                'value' => $value->value,
                'extraData' => json_decode($value->extra_data ?? '', true),
            ];
        }, $values);
    }

    private function relationsFromDB(Collection $relations): array
    {
        return reduce(function (array $acc, RelationDAO $relation): array {
            $acc[$relation->key][$relation->child->id] = $relation->child->class_key;
            return $acc;
        }, $relations->sortBy('order'), []);
    }
}
