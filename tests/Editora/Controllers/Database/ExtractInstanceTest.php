<?php declare(strict_types=1);

namespace Tests\Editora\Controllers\Database;

use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Tests\DatabaseTestCase;

final class ExtractInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function extractInstanceSuccessfullyFromMysql(): void
    {
        $instance1 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-three',
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

        $response = $this->postJson('extract', [
            'query' => '{
                InstanceOne(preview: false, language: es)
            }'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'key' => 'instance-one',
            'language' => 'es',
            'attributes' => [
                [
                    'key' => 'default-attribute',
                    'value' => 'valor1',
                    'attributes' => []
                ]
            ],
            'params' => [
                'preview' => false,
                'language' => 'es'
            ],
            'relations' => []
        ]);
    }
}
