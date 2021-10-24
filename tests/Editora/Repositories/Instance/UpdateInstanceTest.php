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
use Tests\DatabaseTestCase;

final class UpdateInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function updateInstanceSuccessfully(): void
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
            'value' => 'subvalor1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value2EN = ValueDAO::create([
            'attribute_id' => $attribute2->id,
            'language' => 'en',
            'value' => 'subvalue1',
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
            'value' => 'subsubvalor1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value3EN = ValueDAO::create([
            'attribute_id' => $attribute3->id,
            'language' => 'en',
            'value' => 'subsubvalue1',
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
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value4EN = ValueDAO::create([
            'attribute_id' => $attribute4->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $attribute5 = AttributeDAO::create([
            'instance_id' => $instance1->id,
            'parent_id' => $attribute4->id,
            'key' => 'another-default-sub-attribute',
        ]);

        $value5ES = ValueDAO::create([
            'attribute_id' => $attribute5->id,
            'language' => 'es',
            'value' => 'subvalor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value5EN = ValueDAO::create([
            'attribute_id' => $attribute5->id,
            'language' => 'en',
            'value' => 'subvalue2',
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
            'value' => 'subsubvalor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value6EN = ValueDAO::create([
            'attribute_id' => $attribute6->id,
            'language' => 'en',
            'value' => 'subsubvalue2',
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

        $instance4 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-four',
            'key' => 'instance-four',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $instance5 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-five',
            'key' => 'instance-five',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $rel1 = RelationDAO::create([
            'key' => 'relation-key1',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance2->id,
            'order' => 0,
        ]);

        $rel2 = RelationDAO::create([
            'key' => 'relation-key1',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance3->id,
            'order' => 1,
        ]);

        $rel3 = RelationDAO::create([
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance4->id,
            'order' => 1,
        ]);

        $rel4 = RelationDAO::create([
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance5->id,
            'order' => 0,
        ]);

        $structureLoader = $this->mock(StructureLoaderInterface::class);
        $structureLoader->shouldReceive('load')->with('ClassOne')->andReturn([
            'relations' => [
                'relation-key1' => [
                    'class-two',
                    'class-three',
                ],
                'relation-key2' => [
                    'class-four',
                    'class-five',
                ],
            ],
            'attributes' => [
                'DefaultAttribute' => [
                    'attributes' => [
                        'DefaultSubAttribute' => [
                            'attributes' => [
                                'DefaultSubSubAttribute' => [],
                            ],
                        ],
                    ],
                ],
                'AnotherDefaultAttribute' => [
                    'attributes' => [
                        'AnotherDefaultSubAttribute' => [
                            'attributes' => [
                                'AnotherDefaultSubSubAttribute' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ])->once();
        $repository = new InstanceRepository(new InstanceBuilder($structureLoader));
        $instance = $repository->find($instance1->id);

        $instance->fill([
            'metadata' => [
                'key' => 'instance-updated',
                'publication' => [
                    'status' => 'pending',
                    'startPublishingDate' => '1989-03-08 09:00:00',
                    'endPublishingDate' => '2100-03-08 09:00:00',
                ],
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'valor1actualizado',
                        ], [
                            'language' => 'en',
                            'value' => 'value1updated',
                        ],
                    ],
                    'attributes' => [
                        'default-sub-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'subvalor1actualizado',
                                ], [
                                    'language' => 'en',
                                    'value' => 'subvalue1updated',
                                ],
                            ],
                            'attributes' => [
                                'default-sub-sub-attribute' => [
                                    'values' => [
                                        [
                                            'language' => 'es',
                                            'value' => 'subsubvalor1actualizado',
                                        ], [
                                            'language' => 'en',
                                            'value' => 'subsubvalue1updated',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'another-default-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'valor2actualizado',
                        ], [
                            'language' => 'en',
                            'value' => 'value2updated',
                        ],
                    ],
                    'attributes' => [
                        'another-default-sub-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'subvalor2actualizado',
                                ], [
                                    'language' => 'en',
                                    'value' => 'subvalue2updated',
                                ],
                            ],
                            'attributes' => [
                                'another-default-sub-sub-attribute' => [
                                    'values' => [
                                        [
                                            'language' => 'es',
                                            'value' => 'subsubvalor2actualizado',
                                        ], [
                                            'language' => 'en',
                                            'value' => 'subsubvalue2updated',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'relations' => [
                'relation-key1' => [
                    $instance3->id => $instance3->class_key,
                    $instance2->id => $instance2->class_key,
                ],
            ],
        ]);
        $repository->save($instance);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance1->id,
            'uuid' => $instance1->uuid,
            'class_key' => $instance1->class_key,
            'key' => 'instance-updated',
            'status' => 'pending',
            'start_publishing_date' => $instance1->start_publishing_date,
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'id' => $attribute1->id,
            'key' => $attribute1->key,
            'instance_id' => $attribute1->instance_id,
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value1ES->id,
            'attribute_id' => $attribute1->id,
            'language' => $value1ES->language,
            'value' => 'valor1actualizado',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value1EN->id,
            'attribute_id' => $attribute1->id,
            'language' => $value1EN->language,
            'value' => 'value1updated',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'id' => $attribute2->id,
            'key' => $attribute2->key,
            'instance_id' => $attribute2->instance_id,
            'parent_id' => $attribute1->id,
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value2ES->id,
            'attribute_id' => $attribute2->id,
            'language' => $value2ES->language,
            'value' => 'subvalor1actualizado',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value2EN->id,
            'attribute_id' => $attribute2->id,
            'language' => $value2EN->language,
            'value' => 'subvalue1updated',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'id' => $attribute3->id,
            'key' => $attribute3->key,
            'instance_id' => $attribute3->instance_id,
            'parent_id' => $attribute2->id,
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value3ES->id,
            'attribute_id' => $attribute3->id,
            'language' => $value3ES->language,
            'value' => 'subsubvalor1actualizado',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value3EN->id,
            'attribute_id' => $attribute3->id,
            'language' => $value3EN->language,
            'value' => 'subsubvalue1updated',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'id' => $attribute4->id,
            'key' => $attribute4->key,
            'instance_id' => $attribute1->instance_id,
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value4ES->id,
            'attribute_id' => $attribute4->id,
            'language' => $value4ES->language,
            'value' => 'valor2actualizado',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value4EN->id,
            'attribute_id' => $attribute4->id,
            'language' => $value4EN->language,
            'value' => 'value2updated',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'id' => $attribute5->id,
            'key' => $attribute5->key,
            'instance_id' => $attribute5->instance_id,
            'parent_id' => $attribute4->id,
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value5ES->id,
            'attribute_id' => $attribute5->id,
            'language' => $value5ES->language,
            'value' => 'subvalor2actualizado',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value5EN->id,
            'attribute_id' => $attribute5->id,
            'language' => $value5EN->language,
            'value' => 'subvalue2updated',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'id' => $attribute6->id,
            'key' => $attribute6->key,
            'instance_id' => $attribute6->instance_id,
            'parent_id' => $attribute5->id,
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value6ES->id,
            'attribute_id' => $attribute6->id,
            'language' => $value6ES->language,
            'value' => 'subsubvalor2actualizado',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $value6EN->id,
            'attribute_id' => $attribute6->id,
            'language' => $value6EN->language,
            'value' => 'subsubvalue2updated',
        ]);

        $this->assertDatabaseHas('mage_relations', [
            'key' => 'relation-key1',
            'parent_instance_id' => $instance->id(),
            'child_instance_id' => $instance2->id,
            'order' => 1,
        ]);

        $this->assertDatabaseHas('mage_relations', [
            'key' => 'relation-key1',
            'parent_instance_id' => $instance->id(),
            'child_instance_id' => $instance3->id,
            'order' => 0,
        ]);

        $this->assertDatabaseMissing('mage_relations', [
            'key' => 'relation-key2',
            'parent_instance_id' => $instance->id(),
            'child_instance_id' => $instance4->id,
            'order' => 1,
        ]);

        $this->assertDatabaseMissing('mage_relations', [
            'key' => 'relation-key2',
            'parent_instance_id' => $instance->id(),
            'child_instance_id' => $instance5->id,
            'order' => 0,
        ]);
    }
}
