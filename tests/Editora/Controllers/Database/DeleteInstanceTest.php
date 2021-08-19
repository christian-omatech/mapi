<?php

namespace Tests\Editora\Database;

use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Tests\DatabaseTestCase;

final class DeleteInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function deleteInstanceSuccessfullyInMysql(): void
    {
        $instance = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-one',
            'key' => 'instance-one',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $attribute = AttributeDAO::create([
            'instance_id' => $instance->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $valueES = ValueDAO::create([
            'attribute_id' => $attribute->id,
            'language' => 'es',
            'value' => 'valor1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $valueEN = ValueDAO::create([
            'attribute_id' => $attribute->id,
            'language' => 'en',
            'value' => 'value1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

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

        $this->assertDatabaseMissing('mage_attributes', [
            'id' => $attribute->id,
            'instance_id' => $instance->id,
            'key' => 'default-attribute'
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $valueES->id,
            'attribute_id' => $valueES->attribute_id,
            'language' => $valueES->language,
            'value' => $valueES->value,
        ]);

        $this->assertDatabaseMissing('mage_values', [
            'id' => $valueEN->id,
            'attribute_id' => $valueEN->attribute_id,
            'language' => $valueEN->language,
            'value' => $valueEN->value,
        ]);
    }
}
