<?php

namespace Tests\Editora\Database;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Tests\DatabaseTestCase;

final class UpdateInstanceTest extends DatabaseTestCase
{
    /** @test */
    public function updateInstance(): void
    {
        $instance = InstanceDAO::factory()->has(
            ValueDAO::factory()->count(2)->state(new Sequence(
                ['language' => 'es'],
                ['language' => 'en'],
            )), 'values'
        )->create();

        $attributes = [];
        foreach($instance->values as $value) {
            $attributes['values'][$value->attribute_key][] = [
                'id' => $value->id,
                'language' => $value->language,
                'value' => 'test2'
            ];
        }

        $instanceData = [
            'classKey' => $instance->class_key,
            'metadata' => [
                'key' => $instance->key,
                'publication' => [
                    'startPublishingDate' => $instance->start_publishing_date
                ]
            ],
            'attributes' => $attributes
        ];

        $response = $this->putJson($instance->id, $instanceData);
        $response->assertStatus(204);

        $this->assertDatabaseHas('mage_instances', [
            'uuid' => $instance->uuid,
            'class_key' => $instance->class_key,
            'key' => $instance->key,
            'status' => $instance->status,
            'start_publishing_date' => $instance->start_publishing_date,
            'end_publishing_date' => $instance->end_publishing_date,
        ]);

        foreach($instance->values as $value) {
            $this->assertDatabaseMissing('mage_values', [
                'id' => $value->id,
                'attribute_key' => $value->attribute_key,
                'language' => $value->language,
                'value' => 'test2',
                'extra_data' => $value->extra_data,
            ]);
        }
    }
}
