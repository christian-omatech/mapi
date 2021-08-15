<?php

namespace Tests\Editora\Database;

use Tests\DatabaseTestCase;

final class CreateInstanceTest extends DatabaseTestCase
{
    /** @test */
    public function createInstanceSuccessfullyInMysql(): void
    {
        $response = $this->postJson('/', [
            'class_key' => 'ClassOne',
            'metadata' => [
                'key' => 'instance-test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00'
                ]
            ],
            'attributes' => [
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'id' => null,
                            'language' => 'es',
                            'value' => 'test'
                        ],[
                            'id' => null,
                            'language' => 'en',
                            'value' => 'test'
                        ],
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseHas('mage_instances', [
            'uuid' => null,
            'class_key' => 'class-one',
            'key' => 'instance-test',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null
        ]);
        $this->assertDatabaseHas('mage_values', [
            'attribute_key' => 'all-languages-attribute',
            'language' => 'es',
            'value' => 'test',
        ]);
        $this->assertDatabaseHas('mage_values', [
            'attribute_key' => 'all-languages-attribute',
            'language' => 'en',
            'value' => 'test',
        ]);
        $response->assertStatus(204);
    }

//    /** @test */
//    public function updateInstance()
//    {
//        $this->app->bind(InstanceRepositoryInterface::class, InstanceRepository::class);
//
//        $response = $this->postJson('/', [
//            'class_key' => 'test',
//            'metadata' => [
//                'key' => 'test',
//                'publication' => [
//                    'start_publishing_date' => '1989-03-08 09:00:00'
//                ]
//            ],
//            'attributes' => [
//                'all-languages-attribute' => [
//                    'values' => [
//                        [
//                            'id' => null,
//                            'language' => 'es',
//                            'value' => 'test'
//                        ],[
//                            'id' => null,
//                            'language' => 'en',
//                            'value' => 'test'
//                        ],
//                    ]
//                ]
//            ]
//        ]);
//
//        $this->assertDatabaseHas('mage_instances', [
//            'uuid' => null,
//            'class_key' => 'test',
//            'key' => 'test',
//            'status' => 'pending',
//            'start_publishing_date' => '1989-03-08 09:00:00',
//            'end_publishing_date' => null
//        ]);
//
//
////        $this->assertDatabaseHas('mage_values', [
////            'attribute_key' => 'all-languages-attribute',
////            'language' => 'es',
////            'value' => 'test',
////        ]);
////
////        $this->assertDatabaseHas('mage_values', [
////            'attribute_key' => 'all-languages-attribute',
////            'language' => 'en',
////            'value' => 'test',
////        ]);
//
//        $response = $this->putJson('2', [
//            'class_key' => 'test',
//            'metadata' => [
//                'key' => 'test',
//                'publication' => [
//                    'start_publishing_date' => '1989-03-08 09:00:00'
//                ]
//            ],
//            'attributes' => [
//                'all-languages-attribute' => [
//                    'values' => [
//                        [
//                            'id' => 1,
//                            'language' => 'es',
//                            'value' => 'test'
//                        ],[
//                            'id' => 2,
//                            'language' => 'en',
//                            'value' => 'test'
//                        ],
//                    ]
//                ]
//            ]
//        ]);
//
//        dump(ValueDAO::all());
//
//        dd($response);
//    }
}
