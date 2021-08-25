<?php declare(strict_types=1);

namespace Tests\Editora\Controllers\Database;

use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Omatech\Mcore\Editora\Domain\Instance\PublicationStatus;
use Tests\DatabaseTestCase;

final class UpdateInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function updateInstanceSuccessfully(): void
    {
        $instance = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-one',
            'key' => 'instance-test22',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $attribute = AttributeDAO::create([
            'instance_id' => $instance->id,
            'parent_id' => null,
            'key' => 'all-languages-attribute',
        ]);

        $attribute2 = AttributeDAO::create([
            'instance_id' => $instance->id,
            'parent_id' => null,
            'key' => 'nice-url',
        ]);

        $valueES = ValueDAO::create([
            'attribute_id' => $attribute->id,
            'language' => 'es',
            'value' => 'test',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);
        $valueEN = ValueDAO::create([
            'attribute_id' => $attribute->id,
            'language' => 'en',
            'value' => 'test',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $value2ES = ValueDAO::create([
            'attribute_id' => $attribute2->id,
            'language' => 'es',
            'value' => '/es/soy-una-url',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);
        $value2EN = ValueDAO::create([
            'attribute_id' => $attribute2->id,
            'language' => 'en',
            'value' => '/en/soy-una-url',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instanceData = [
            'classKey' => $instance->class_key,
            'key' => $instance->key,
            'startPublishingDate' => $instance->start_publishing_date,
            'status' => PublicationStatus::PUBLISHED,
            'attributes' => [
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'id' => $valueES->id,
                            'language' => $valueES->language,
                            'value' => 'dia'
                        ], [
                            'id' => $valueEN->id,
                            'language' => $valueEN->language,
                            'value' => 'day'
                        ],
                    ],
                ],
                'nice-url' => [
                    'values' => [
                        [
                            'id' => $value2ES->id,
                            'language' => $value2ES->language,
                            'value' => '/es/soy-una-url'
                        ], [
                            'id' => $value2EN->id,
                            'language' => $value2EN->language,
                            'value' => '/en/soy-una-url'
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->putJson($instance->id, $instanceData);
        $response->assertStatus(204);

        $this->assertDatabaseHas('mage_instances', [
            'id' => $instance->id,
            'uuid' => $instance->uuid,
            'class_key' => $instance->class_key,
            'key' => $instance->key,
            'status' => PublicationStatus::PUBLISHED,
            'start_publishing_date' => $instance->start_publishing_date,
            'end_publishing_date' => $instance->end_publishing_date,
        ]);

        $this->assertDatabaseHas('mage_attributes', [
            'id' => $attribute->id,
            'instance_id' => $instance->id,
            'key' => 'all-languages-attribute',
        ]);
        $this->assertDatabaseHas('mage_values', [
            'id' => $valueES->id,
            'attribute_id' => $valueES->attribute_id,
            'language' => $valueES->language,
            'value' => 'dia',
        ]);
        $this->assertDatabaseHas('mage_values', [
            'id' => $valueEN->id,
            'attribute_id' => $valueEN->attribute_id,
            'language' => $valueEN->language,
            'value' => 'day',
        ]);
    }
}
