<?php declare(strict_types=1);

namespace Tests\Editora\Repositories\Instance;

use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureLoaderInterface;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\InstanceBuilder;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\RelationDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\InstanceRepository;
use Tests\DatabaseTestCase;

final class ReadInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function readInstanceSuccessfully(): void
    {
        $instance1 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-one',
            'key' => 'instance-one',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
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
            'value' => 'valor4',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value4EN = ValueDAO::create([
            'attribute_id' => $attribute4->id,
            'language' => 'en',
            'value' => 'value4',
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
            'value' => 'valor5',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value5EN = ValueDAO::create([
            'attribute_id' => $attribute5->id,
            'language' => 'en',
            'value' => 'value5',
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
            'value' => 'valor6',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value6EN = ValueDAO::create([
            'attribute_id' => $attribute6->id,
            'language' => 'en',
            'value' => 'value6',
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

        RelationDAO::create([
            'key' => 'relation-key1',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance2->id,
            'order' => 0,
        ]);

        RelationDAO::create([
            'key' => 'relation-key1',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance3->id,
            'order' => 1,
        ]);

        RelationDAO::create([
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance4->id,
            'order' => 1,
        ]);

        RelationDAO::create([
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance5->id,
            'order' => 0,
        ]);
        sleep(1);
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

        $this->assertEquals([
            'class' => [
                'key' => 'class-one',
                'relations' => [
                    [
                        'key' => 'relation-key1',
                        'classes' => [
                            'class-two',
                            'class-three',
                        ],
                    ], [
                        'key' => 'relation-key2',
                        'classes' => [
                            'class-four',
                            'class-five',
                        ],
                    ],
                ],
            ],
            'metadata' => [
                'key' => $instance1->key,
                'id' => $instance1->id,
                'publication' => [
                    'status' => $instance1->status,
                    'startPublishingDate' => $instance1->start_publishing_date,
                    'endPublishingDate' => $instance1->end_publishing_date,
                ],
            ],
            'attributes' => [
                [
                    'key' => $attribute1->key,
                    'type' => 'string',
                    'values' => [
                        [
                            'language' => $value1ES->language,
                            'rules' => [],
                            'configuration' => [],
                            'value' => $value1ES->value,
                        ], [
                            'language' => $value1EN->language,
                            'rules' => [],
                            'configuration' => [],
                            'value' => $value1EN->value,
                        ],
                    ],
                    'attributes' => [
                        [
                            'key' => $attribute2->key,
                            'type' => 'string',
                            'values' => [
                                [
                                    'language' => $value2ES->language,
                                    'rules' => [],
                                    'configuration' => [],
                                    'value' => $value2ES->value,
                                ], [
                                    'language' => $value2EN->language,
                                    'rules' => [],
                                    'configuration' => [],
                                    'value' => $value2EN->value,
                                ],
                            ],
                            'attributes' => [
                                [
                                    'key' => $attribute3->key,
                                    'type' => 'string',
                                    'values' => [
                                        [
                                            'language' => $value3ES->language,
                                            'rules' => [],
                                            'configuration' => [],
                                            'value' => $value3ES->value,
                                        ], [
                                            'language' => $value3EN->language,
                                            'rules' => [],
                                            'configuration' => [],
                                            'value' => $value3EN->value,
                                        ],
                                    ],
                                    'attributes' => [],
                                ],
                            ],
                        ],
                    ],
                ], [
                    'key' => $attribute4->key,
                    'type' => 'string',
                    'values' => [
                        [
                            'language' => $value4ES->language,
                            'rules' => [],
                            'configuration' => [],
                            'value' => $value4ES->value,
                        ], [
                            'language' => $value4EN->language,
                            'rules' => [],
                            'configuration' => [],
                            'value' => $value4EN->value,
                        ],
                    ],
                    'attributes' => [
                        [
                            'key' => $attribute5->key,
                            'type' => 'string',
                            'values' => [
                                [
                                    'language' => $value5ES->language,
                                    'rules' => [],
                                    'configuration' => [],
                                    'value' => $value5ES->value,
                                ], [
                                    'language' => $value5EN->language,
                                    'rules' => [],
                                    'configuration' => [],
                                    'value' => $value5EN->value,
                                ],
                            ],
                            'attributes' => [
                                [
                                    'key' => $attribute6->key,
                                    'type' => 'string',
                                    'values' => [
                                        [
                                            'language' => $value6ES->language,
                                            'rules' => [],
                                            'configuration' => [],
                                            'value' => $value6ES->value,
                                        ], [
                                            'language' => $value6EN->language,
                                            'rules' => [],
                                            'configuration' => [],
                                            'value' => $value6EN->value,
                                        ],
                                    ],
                                    'attributes' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'relations' => [
                [
                    'key' => 'relation-key1',
                    'instances' => [
                        $instance2->id => $instance2->class_key,
                        $instance3->id => $instance3->class_key,
                    ],
                ], [
                    'key' => 'relation-key2',
                    'instances' => [
                        $instance5->id => $instance5->class_key,
                        $instance4->id => $instance4->class_key,
                    ],
                ],
            ],
        ], $instance->toArray());
    }
}
