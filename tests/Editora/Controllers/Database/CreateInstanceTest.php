<?php declare(strict_types=1);

namespace Tests\Editora\Controllers\Database;

use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Tests\DatabaseTestCase;

final class CreateInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function createInstanceSuccessfullyInMysql(): void
    {
        $response = $this->postJson('/', [
            'classKey' => 'ClassOne',
            'key' => 'instance-test',
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'test',
                        ],[
                            'language' => 'en',
                            'value' => 'test',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('mage_instances', [
            'uuid' => null,
            'class_key' => 'class-one',
            'key' => 'instance-test',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'key' => 'all-languages-attribute',
        ]);
        $this->assertDatabaseHas('mage_values', [
            'language' => 'es',
            'value' => 'test',
        ]);
        $this->assertDatabaseHas('mage_values', [
            'language' => 'en',
            'value' => 'test',
        ]);
        $response->assertStatus(204);
    }

    /** @test */
    public function reCreateInstanceFail(): void
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

        $response = $this->postJson('/', [
            'classKey' => $instance1->class_key,
            'key' => $instance1->key,
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'default-attribute' => [
                    'values' => [
                        [
                            'language' => $value1ES->language,
                            'value' => $value1ES->value,
                        ],[
                            'language' => $value1EN->language,
                            'value' => $value1EN->value,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => 422, 'message' => '', 'error' => '']);
    }

    /** @test */
    public function uniqueRule(): void
    {
        $response = $this->postJson('/', [
            'classKey' => 'ClassOne',
            'key' => 'class-one',
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'nice-url' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => '/es/soy-una-url',
                        ],[
                            'language' => 'en',
                            'value' => '/en/soy-una-url',
                        ],
                    ],
                ],
            ],
        ]);
        $response->assertStatus(204);

        $response = $this->postJson('/', [
            'classKey' => 'ClassTwo',
            'key' => 'class-two',
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'nice-url' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => '/es/soy-una-url',
                        ],[
                            'language' => 'en',
                            'value' => '/en/soy-una-url',
                        ],
                    ],
                ],
            ],
        ]);
        $response->assertStatus(422);
    }
}
