<?php declare(strict_types=1);

namespace Tests\Editora\Controllers\Database;

use Tests\Editora\EditoraTestCase;
use Tests\Editora\ObjectMother\NewsMother;

final class CreateInstanceTest extends EditoraTestCase
{
    /** @test */
    public function createInstanceSuccessfullyInMysql(): void
    {
        $this->postJson('/', [
            'uuid' => $this->faker->uuid(),
            'classKey' => 'News',
            'key' => 'news-instance',
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'title' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => 'title-es',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => 'title-en',
                        ],
                    ],
                ],
                'description' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => 'description-es',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => 'description-en',
                        ]
                    ]
                ],
                'nice-url' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => '/es/soy-una-url',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => '/en/soy-una-url',
                        ],
                    ],
                ],
            ],
        ])->assertStatus(204);

        $this->assertDatabaseHas('mage_instances', [
            'class_key' => 'news',
            'key' => 'news-instance',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ])->assertDatabaseHas('mage_attributes', [
            'key' => 'title',
        ])->assertDatabaseHas('mage_attributes', [
            'key' => 'description',
        ])->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'title-es',
        ])->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'title-en',
        ])->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'description-es',
        ])->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'description-en',
        ]);
    }

    /** @test */
    public function creatingExistingInstanceKeyFail(): void
    {
        $new = (new NewsMother())->database();

        $response = $this->postJson('/', [
            'uuid' => $new->uuid,
            'classKey' => $new->class_key,
            'key' => $new->key,
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'title' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => 'title-es',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => 'title-en',
                        ],
                    ],
                ],
                'description' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => 'description-es',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => 'description-en',
                        ]
                    ]
                ],
                'nice-url' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => '/es/soy-otra-url',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => '/en/soy-otra-url',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => 422, 'message' => '', 'error' => '']);
    }

    /** @test */
    public function uniqueValueOnDatabase(): void
    {
        (new NewsMother())->database();

        $response = $this->postJson('/', [
            'uuid' => $this->faker->uuid(),
            'classKey' => 'news',
            'key' => 'new-instance-2',
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'title' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => 'title-es',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => 'title-en',
                        ],
                    ],
                ],
                'description' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => 'description-es',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => 'description-en',
                        ]
                    ]
                ],
                'nice-url' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => '/es/soy-una-url',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => '/en/soy-una-url',
                        ],
                    ],
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function failedValidationOnCreateInstance(): void
    {
        $response = $this->postJson('/', []);
        $response->assertJson([
            'status' => '422',
            'error' => '',
            'message' => 'Class  not found.',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function invalidHeadersOnPostCall(): void
    {
        $response = $this->post('/', [
            'uuid' => $this->faker->uuid(),
            'classKey' => 'news',
            'key' => 'new-instance-2',
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'title' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => 'title-es',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => 'title-en',
                        ],
                    ],
                ],
                'description' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => 'description-es',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => 'description-en',
                        ]
                    ]
                ],
                'nice-url' => [
                    'values' => [
                        [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'es',
                            'value' => '/es/soy-una-url',
                        ], [
                            'uuid' => $this->faker->uuid(),
                            'language' => 'en',
                            'value' => '/en/soy-una-url',
                        ],
                    ],
                ],
            ],
        ]);
        $response->assertJson([
            'status' => '422',
            'error' => '',
            'message' => 'Class  not found.',
        ]);
        $response->assertStatus(422);
    }
}
