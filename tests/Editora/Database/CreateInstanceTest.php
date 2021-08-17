<?php

namespace Tests\Editora\Database;

use Exception;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceExistsException;
use PDOException;
use Tests\DatabaseTestCase;

final class CreateInstanceTest extends DatabaseTestCase
{
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

    /** @test */
    public function reCreateInstanceFail(): void
    {
        $instance = InstanceDAO::factory()->has(
            ValueDAO::factory()->count(2)->state(new Sequence(
                        ['language' => 'es'],
                        ['language' => 'en'],
                    )),
            'values'
        )->state([
            'key' => 'reinstance'
        ])->create();

        $response = $this->postJson('/', [
            'classKey' => $instance->class_key,
            'key' => $instance->key,
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
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

        $response->assertStatus(422);
        $response->assertJson(['status' => 422, 'message' => '', 'error' => '']);
    }
}
