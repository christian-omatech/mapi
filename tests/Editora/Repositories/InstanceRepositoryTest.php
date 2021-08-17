<?php
namespace Tests\Editora\Repositories;

use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureLoaderInterface;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\InstanceBuilder;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\InstanceRepository;
use Tests\DatabaseTestCase;

final class CreateInstanceTest extends DatabaseTestCase
{
    /** @test */
    public function testSaveSuccessfully(): void
    {
        $structureLoader = $this->mock(StructureLoaderInterface::class);
        $structureLoader->shouldReceive('load')->with('ClassFive')->andReturn([
            'attributes' => [
                'DefaultAttribute' => [],
            ]
        ])->times(2);

        $instanceRepository = new InstanceRepository(new InstanceBuilder($structureLoader));
        $instance = $instanceRepository->build('classFive');
        $instance2 = $instanceRepository->build('classFive');

        $instance->fill([
            'metadata' => [
                'key' => 'instance-five',
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'id' => null,
                            'language' => 'es',
                            'value' => 'hola',
                        ], [
                            'id' => null,
                            'language' => 'en',
                            'value' => 'adios',
                        ],
                    ],
                ],
            ],
            'relations' => []
        ]);

        $instanceRepository->save($instance);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance->id(),
            'class_key' => 'class-five',
            'key' => 'instance-five',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $instance->toArray()['attributes'][0]['values'][0]['id'],
            'attribute_key' => 'default-attribute',
            'language' => 'es',
            'value' => 'hola',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $instance->toArray()['attributes'][0]['values'][1]['id'],
            'attribute_key' => 'default-attribute',
            'language' => 'en',
            'value' => 'adios',
        ]);

        $instance2->fill([
            'metadata' => [
                'key' => 'instance-ten',
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'id' => null,
                            'language' => 'es',
                            'value' => 'hello',
                        ], [
                            'id' => null,
                            'language' => 'en',
                            'value' => 'bye',
                        ],
                    ],
                ],
            ],
            'relations' => []
        ]);

        $instanceRepository->save($instance2);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance2->id(),
            'class_key' => 'class-five',
            'key' => 'instance-ten',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $instance2->toArray()['attributes'][0]['values'][0]['id'],
            'attribute_key' => 'default-attribute',
            'language' => 'es',
            'value' => 'hello',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $instance2->toArray()['attributes'][0]['values'][1]['id'],
            'attribute_key' => 'default-attribute',
            'language' => 'en',
            'value' => 'bye',
        ]);

        $instance->fill([
            'metadata' => [
                'key' => 'instance-one',
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'id' => $instance->toArray()['attributes'][0]['values'][0]['id'],
                            'language' => 'es',
                            'value' => 'adios',
                        ], [
                            'id' => $instance->toArray()['attributes'][0]['values'][1]['id'],
                            'language' => 'en',
                            'value' => 'hola',
                        ],
                    ],
                ],
            ],
            'relations' => []
        ]);
        $instanceRepository->save($instance);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance->id(),
            'class_key' => 'class-five',
            'key' => 'instance-one',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $instance->toArray()['attributes'][0]['values'][0]['id'],
            'attribute_key' => 'default-attribute',
            'language' => 'es',
            'value' => 'adios',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $instance->toArray()['attributes'][0]['values'][1]['id'],
            'attribute_key' => 'default-attribute',
            'language' => 'en',
            'value' => 'hola',
        ]);

        $instance2->fill([
            'metadata' => [
                'key' => 'instance-two',
                'publication' => [
                    'startPublishingDate' => '1989-03-08 09:00:00',
                ],
            ],
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'id' => $instance2->toArray()['attributes'][0]['values'][0]['id'],
                            'language' => 'es',
                            'value' => 'bye',
                        ], [
                            'id' => $instance2->toArray()['attributes'][0]['values'][1]['id'],
                            'language' => 'en',
                            'value' => 'hello',
                        ],
                    ],
                ],
            ],
            'relations' => []
        ]);
        $instanceRepository->save($instance2);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance2->id(),
            'class_key' => 'class-five',
            'key' => 'instance-two',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $instance2->toArray()['attributes'][0]['values'][0]['id'],
            'attribute_key' => 'default-attribute',
            'language' => 'es',
            'value' => 'bye',
        ]);

        $this->assertDatabaseHas('mage_values', [
            'id' => $instance2->toArray()['attributes'][0]['values'][1]['id'],
            'attribute_key' => 'default-attribute',
            'language' => 'en',
            'value' => 'hello',
        ]);
    }
}
