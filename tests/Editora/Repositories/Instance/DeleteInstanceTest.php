<?php declare(strict_types=1);

namespace Tests\Editora\Repositories\Instance;

use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureLoaderInterface;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\InstanceBuilder;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\RelationDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance\InstanceRepository;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Tests\DatabaseTestCase;

final class DeleteInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function deleteInstanceSuccessfully(): void
    {
        $instance1 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-one',
            'key' => 'instance-one',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $attribute1 = AttributeDAO::create([
            'instance_id' => $instance1->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $value1ES = ValueDAO::create([
            'attribute_id' => $attribute1->id,
            'language' => 'es',
            'value' => 'valor1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value1EN = ValueDAO::create([
            'attribute_id' => $attribute1->id,
            'language' => 'en',
            'value' => 'value1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $attribute2 = AttributeDAO::create([
            'instance_id' => $instance1->id,
            'parent_id' => $attribute1->id,
            'key' => 'default-sub-attribute',
        ]);

        $value2ES = ValueDAO::create([
            'attribute_id' => $attribute2->id,
            'language' => 'es',
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value2EN = ValueDAO::create([
            'attribute_id' => $attribute2->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $attribute3 = AttributeDAO::create([
            'instance_id' => $instance1->id,
            'parent_id' => $attribute2->id,
            'key' => 'default-sub-sub-attribute',
        ]);

        $value3ES = ValueDAO::create([
            'attribute_id' => $attribute3->id,
            'language' => 'es',
            'value' => 'valor3',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value3EN = ValueDAO::create([
            'attribute_id' => $attribute3->id,
            'language' => 'en',
            'value' => 'value3',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $attribute4 = AttributeDAO::create([
            'instance_id' => $instance1->id,
            'parent_id' => null,
            'key' => 'another-default-attribute',
        ]);

        $value4ES = ValueDAO::create([
            'attribute_id' => $attribute4->id,
            'language' => 'es',
            'value' => 'valor6',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value4EN = ValueDAO::create([
            'attribute_id' => $attribute4->id,
            'language' => 'en',
            'value' => 'value6',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $attribute5 = AttributeDAO::create([
            'instance_id' => $instance1->id,
            'parent_id' => $attribute3->id,
            'key' => 'another-default-sub-attribute',
        ]);

        $value5ES = ValueDAO::create([
            'attribute_id' => $attribute5->id,
            'language' => 'es',
            'value' => 'valor7',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value5EN = ValueDAO::create([
            'attribute_id' => $attribute5->id,
            'language' => 'en',
            'value' => 'value7',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $attribute6 = AttributeDAO::create([
            'instance_id' => $instance1->id,
            'parent_id' => $attribute5->id,
            'key' => 'another-default-sub-sub-attribute',
        ]);

        $value6ES = ValueDAO::create([
            'attribute_id' => $attribute6->id,
            'language' => 'es',
            'value' => 'valor8',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value6EN = ValueDAO::create([
            'attribute_id' => $attribute6->id,
            'language' => 'en',
            'value' => 'value8',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instance2 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-two',
            'key' => 'instance-two',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $instance3 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-three',
            'key' => 'instance-three',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $relation1 = RelationDAO::create([
            'key' => 'relation-key1',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance2->id,
            'order' => 0,
        ]);

        $relation2 = RelationDAO::create([
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance3->id,
            'order' => 0,
        ]);

        $structureLoader = $this->mock(StructureLoaderInterface::class);
        $structureLoader->shouldReceive('load')->with('ClassOne')->andReturn([
            'attributes' => [],
            'relations' => [
                'relation-key1' => [
                    'class-two',
                ],
                'relation-key2' => [
                    'class-three',
                ],
            ],
        ])->once();

        $instanceCache = $this->mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->with('class-one')->andReturn(null);
        $instanceCache->shouldReceive('put')->andReturn(null);

        $repository = new InstanceRepository(new InstanceBuilder($structureLoader, $instanceCache));
        $instance = $repository->find($instance1->id);
        $repository->delete($instance);

        $this->assertDatabaseMissing('mage_instances', [
            'id' => $instance1->id,
            'uuid' => $instance1->uuid,
            'class_key' => $instance1->class_key,
            'key' => $instance1->key,
            'status' => $instance1->status,
            'start_publishing_date' => $instance1->start_publishing_date,
            'end_publishing_date' => null,
        ]);

        $this->assertDatabaseMissing('mage_attributes', [
            'id' => $attribute1->id,
            'key' => $attribute1->key,
            'instance_id' => $attribute1->instance_id,
            'parent_id' => null,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value1ES->id,
            'attribute_id' => $attribute1->id,
            'language' => $value1ES->language,
            'value' => $value1ES->value,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value1EN->id,
            'attribute_id' => $attribute1->id,
            'language' => $value1EN->language,
            'value' => $value1EN->value,
        ]);

        $this->assertDatabaseMissing('mage_attributes', [
            'id' => $attribute2->id,
            'key' => $attribute2->key,
            'instance_id' => $attribute2->instance_id,
            'parent_id' => $attribute1->id,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value2ES->id,
            'attribute_id' => $attribute2->id,
            'language' => $value2ES->language,
            'value' => $value2ES->value,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value2EN->id,
            'attribute_id' => $attribute2->id,
            'language' => $value2EN->language,
            'value' => $value2EN->value,
        ]);

        $this->assertDatabaseMissing('mage_attributes', [
            'id' => $attribute3->id,
            'key' => $attribute3->key,
            'instance_id' => $attribute3->instance_id,
            'parent_id' => $attribute2->id,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value3ES->id,
            'attribute_id' => $attribute3->id,
            'language' => $value3ES->language,
            'value' => $value3ES->value,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value3EN->id,
            'attribute_id' => $attribute3->id,
            'language' => $value3EN->language,
            'value' => $value3EN->value,
        ]);

        $this->assertDatabaseMissing('mage_attributes', [
            'id' => $attribute4->id,
            'key' => $attribute4->key,
            'instance_id' => $attribute1->instance_id,
            'parent_id' => null,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value4ES->id,
            'attribute_id' => $attribute4->id,
            'language' => $value4ES->language,
            'value' => $value4ES->value,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value4EN->id,
            'attribute_id' => $attribute4->id,
            'language' => $value4EN->language,
            'value' => $value4EN->value,
        ]);

        $this->assertDatabaseMissing('mage_attributes', [
            'id' => $attribute5->id,
            'key' => $attribute5->key,
            'instance_id' => $attribute5->instance_id,
            'parent_id' => $attribute4->id,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value5ES->id,
            'attribute_id' => $attribute5->id,
            'language' => $value5ES->language,
            'value' => $value5ES->value,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value5EN->id,
            'attribute_id' => $attribute5->id,
            'language' => $value5EN->language,
            'value' => $value5EN->value,
        ]);

        $this->assertDatabaseMissing('mage_attributes', [
            'id' => $attribute6->id,
            'key' => $attribute6->key,
            'instance_id' => $attribute6->instance_id,
            'parent_id' => $attribute5->id,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value6ES->id,
            'attribute_id' => $attribute6->id,
            'language' => $value6ES->language,
            'value' => $value6ES->value,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $value6EN->id,
            'attribute_id' => $attribute6->id,
            'language' => $value6EN->language,
            'value' => $value6EN->value,
        ]);

        $this->assertDatabaseMissing('mage_relations', [
            'id' => $relation1->id,
            'key' => 'relation-key1',
            'parent_instance_id' => $instance1->i,
            'child_instance_id' => $instance2->id,
            'order' => 0,
        ]);

        $this->assertDatabaseMissing('mage_relations', [
            'id' => $relation2->id,
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->i,
            'child_instance_id' => $instance3->id,
            'order' => 0,
        ]);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance2->id,
            'uuid' => $instance2->uuid,
            'class_key' => $instance2->class_key,
            'key' => $instance2->key,
            'status' => $instance2->status,
            'start_publishing_date' => $instance2->start_publishing_date,
            'end_publishing_date' => null,
        ]);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance3->id,
            'uuid' => $instance3->uuid,
            'class_key' => $instance3->class_key,
            'key' => $instance3->key,
            'status' => $instance3->status,
            'start_publishing_date' => $instance3->start_publishing_date,
            'end_publishing_date' => null,
        ]);
    }
}
