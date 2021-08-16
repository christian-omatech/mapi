<?php

namespace Tests\Editora\Database;

use Tests\DatabaseTestCase;

final class CreateInstanceTest extends DatabaseTestCase
{
    /** @test */
    public function createInstanceSuccessfullyInMysql(): void
    {
        $response = $this->postJson('/', [
            'classKey' => 'ClassOne',
            'key' => 'instance-test',
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
}
