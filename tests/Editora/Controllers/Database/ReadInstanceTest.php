<?php

namespace Tests\Editora\Database;

use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Tests\DatabaseTestCase;

final class ReadInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function readInstance(): void
    {
        $instance = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-one',
            'key' => 'instance-test2',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ]);

        $attribute = AttributeDAO::create([
            'instance_id' => $instance->id,
            'parent_id' => null,
            'key' => 'all-languages-attribute',
        ]);

        ValueDAO::create([
            'attribute_id' => $attribute->id,
            'language' => 'es',
            'value' => 'test',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);
        ValueDAO::create([
            'attribute_id' => $attribute->id,
            'language' => 'en',
            'value' => 'test',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $response = $this->getJson($instance->id);

        $response->assertStatus(200);
        $response->assertJson([
            'class' => [
                'key' => 'class-one',
                'relations' => []
            ],
            'metadata' => [
                'id' => $instance->id,
                'key' => $instance->key,
                'publication' => [
                    'status' => $instance->status,
                    'startPublishingDate' => $instance->start_publishing_date,
                    'endPublishingDate' => $instance->end_publishing_date,
                ]
            ],
            'attributes' => [
                [
                    'key' => 'all-languages-attribute',
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'test',
                        ], [
                            'language' => 'en',
                            'value' => 'test',
                        ]
                    ]
                ]
            ],
            'relations' => []
        ]);
    }
}
