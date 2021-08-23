<?php declare(strict_types=1);

namespace Tests\Editora\Repositories\Instance;

use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureLoaderInterface;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\InstanceBuilder;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance\InstanceRepository;
use Tests\DatabaseTestCase;

final class CreateInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function createInstanceSuccessfully(): void
    {
        $instance2 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-two',
            'key' => 'instance-two',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $instance3 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-three',
            'key' => 'instance-three',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $instance4 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-four',
            'key' => 'instance-four',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $instance5 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-five',
            'key' => 'instance-five',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
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
        $instance = $repository->build('ClassOne');

        $instance->fill([
            'metadata' => [
                'key' => 'instance-one',
                'publication' => [
                    'status' => 'in-revision',
                    'startPublishingDate' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'valor1',
                        ], [
                            'language' => 'en',
                            'value' => 'value1',
                        ],
                    ],
                    'attributes' => [
                        'default-sub-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'subvalor1',
                                ], [
                                    'language' => 'en',
                                    'value' => 'subvalue1',
                                ],
                            ],
                            'attributes' => [
                                'default-sub-sub-attribute' => [
                                    'values' => [
                                        [
                                            'language' => 'es',
                                            'value' => 'subsubvalor1',
                                        ], [
                                            'language' => 'en',
                                            'value' => 'subsubvalue1',
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
                            'value' => 'valor2',
                        ], [
                            'language' => 'en',
                            'value' => 'value2',
                        ],
                    ],
                    'attributes' => [
                        'another-default-sub-attribute' => [
                            'values' => [
                                [
                                    'language' => 'es',
                                    'value' => 'subvalor2',
                                ], [
                                    'language' => 'en',
                                    'value' => 'subvalue2',
                                ],
                            ],
                            'attributes' => [
                                'another-default-sub-sub-attribute' => [
                                    'values' => [
                                        [
                                            'language' => 'es',
                                            'value' => 'subsubvalor2',
                                        ], [
                                            'language' => 'en',
                                            'value' => 'subsubvalue2',
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
                    $instance2->id => $repository->classKey($instance2->id),
                    $instance3->id => $repository->classKey($instance3->id),
                ],
                'relation-key2' => [
                    $instance4->id => $repository->classKey($instance4->id),
                    $instance5->id => $repository->classKey($instance5->id),
                ],
            ],
        ]);
        $repository->save($instance);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance->id(),
            'class_key' => 'class-one',
            'key' => 'instance-one',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'instance_id' => $instance->id(),
            'key' => 'default-attribute',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'instance_id' => $instance->id(),
            'key' => 'default-sub-attribute',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'instance_id' => $instance->id(),
            'key' => 'default-sub-sub-attribute',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'instance_id' => $instance->id(),
            'key' => 'another-default-attribute',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'instance_id' => $instance->id(),
            'key' => 'another-default-sub-attribute',
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'instance_id' => $instance->id(),
            'key' => 'another-default-sub-sub-attribute',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'valor1',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'value1',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'valor2',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'value2',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'subvalor1',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'subvalue1',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'subvalor2',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'subvalue2',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'subsubvalor1',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'subsubvalue1',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'subsubvalor2',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'subsubvalue2',
        ]);

        $this->assertDatabaseHas('mage_relations', [
            'key' => 'relation-key1',
            'parent_instance_id' => $instance->id(),
            'child_instance_id' => $instance2->id,
            'order' => 0,
        ]);

        $this->assertDatabaseHas('mage_relations', [
            'key' => 'relation-key1',
            'parent_instance_id' => $instance->id(),
            'child_instance_id' => $instance3->id,
            'order' => 1,
        ]);

        $this->assertDatabaseHas('mage_relations', [
            'key' => 'relation-key2',
            'parent_instance_id' => $instance->id(),
            'child_instance_id' => $instance4->id,
            'order' => 0,
        ]);

        $this->assertDatabaseHas('mage_relations', [
            'key' => 'relation-key2',
            'parent_instance_id' => $instance->id(),
            'child_instance_id' => $instance5->id,
            'order' => 1,
        ]);
    }
}
