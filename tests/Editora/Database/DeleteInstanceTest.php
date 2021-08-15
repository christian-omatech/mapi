<?php

namespace Tests\Editora\Database;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Tests\DatabaseTestCase;

final class DeleteInstanceTest extends DatabaseTestCase
{
    /** @test */
    public function deleteInstanceSuccessfullyInMysql(): void
    {
        $instance = InstanceDAO::factory()->has(
            ValueDAO::factory()->count(2)->state(new Sequence(
                ['language' => 'es'],
                ['language' => 'en'],
            )), 'values'
        )->create();
        $this->deleteJson($instance->id)->assertStatus(204);
        $this->assertDatabaseMissing('mage_instances', [
            'id' => $instance->id,
            'uuid' => $instance->uuid,
            'class_key' => $instance->class_key,
            'key' => $instance->key,
            'status' => $instance->status,
            'start_publishing_date' => $instance->start_publishing_date,
            'end_publishing_date' => $instance->end_publishing_date
        ]);

        foreach($instance->values as $value) {
            $this->assertDatabaseMissing('mage_values', [
                'id' => $value->id,
                'attribute_key' => $value->attribute_key,
                'language' => $value->language,
                'value' => $value->value,
                'extra_data' => $value->extra_data,
            ]);
        }
    }
}
