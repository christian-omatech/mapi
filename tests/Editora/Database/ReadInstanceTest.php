<?php

namespace Tests\Editora\Database;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Tests\DatabaseTestCase;

final class ReadInstanceTest extends DatabaseTestCase
{
    /** @test */
    public function readInstance(): void
    {
        $instance = InstanceDAO::factory()->has(
            ValueDAO::factory()->count(2)->state(new Sequence(
                ['language' => 'es'],
                ['language' => 'en'],
            )), 'values'
        )->state([
            'status' => 'in-revision',
            'end_publishing_date' => '2021-08-16 22:00:00'
        ])->create();

        $values = [];
        foreach($instance->values as $value) {
            $values[$value->attribute_key][] = [
                'id' => $value->id,
                'language' => $value->language,
                'value' => $value->value,
                'rules' => [],
                'configuration' => []
            ];
        }

        $attributes = [];
        foreach($values as $attributeKey => $values) {
            $attributes[] = [
                'key' => $attributeKey,
                'type' => 'string',
                'values' => $values,
                'attributes' => []
            ];
        }

        $response = $this->getJson($instance->id);
        $response->assertStatus(200);
        $response->assertExactJson([
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
            'attributes' => $attributes,
            'relations' => []
        ]);
    }
}
