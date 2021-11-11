<?php declare(strict_types=1);

namespace Tests\Editora\Controllers\Database;

use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\WithFaker;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\RelationDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;
use Tests\DatabaseTestCase;

final class ExtractInstanceTest extends DatabaseTestCase
{
    use WithFaker;

    /** @test */
    public function extractParentRelations(): void
    {
        $homees = InstanceFactory::new()
            ->has(AttributeFactory::new()
                ->state(['key' => 'nice-url'])
                ->has(ValueFactory::new()
                    ->state(['value' => 'home-es']), 
                'values'),
            'attributes')
        ->createOne(['class_key' => 'home', 'key' => 'home-es']);        
        
        $country1 = InstanceFactory::new()
            ->has(AttributeFactory::new()
                ->state(['key' => 'name'])
                ->has(ValueFactory::new()
                    ->state(['value' => 'country-en']), 
                'values'),
            'attributes')
        ->createOne(['class_key' => 'country', 'key' => 'country-en']);        
        
        $country2 = InstanceFactory::new()
            ->has(AttributeFactory::new()
                ->state(['key' => 'name'])
                ->has(ValueFactory::new()
                    ->state(['value' => 'country-es']), 
                'values'),
            'attributes')
        ->createOne(['class_key' => 'country', 'key' => 'country-es']);

        RelationDAO::create([
            'key' => 'country-home',
            'parent_instance_id' => $homees->id,
            'child_instance_id' => $country1->id,
            'order' => 0,
        ]);              
        
        RelationDAO::create([
            'key' => 'country-home',
            'parent_instance_id' => $homees->id,
            'child_instance_id' => $country2->id,
            'order' => 1,
        ]);            
        
        RelationDAO::create([
            'key' => 'country-home',
            'parent_instance_id' => $country1->id,
            'child_instance_id' => $homees->id,
            'order' => 0,
        ]);        

        $response = $this->postJson('extract', [
            'query' => '{
                Home(language: es) {
                    CountryHome()
                    CountryHome(type: parent)
                }
            }'
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function extractPaginatedInstancesSuccessfullyFromMysql(): void
    {
        $instance1 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-six',
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

        $valueES1 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute1->id,
            'language' => 'es',
            'value' => 'valor1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute1->id,
            'language' => 'en',
            'value' => 'value1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instance2 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-six',
            'key' => 'instance-two',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $attribute2 = AttributeDAO::create([
            'instance_id' => $instance2->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $valueES2 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute2->id,
            'language' => 'es',
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute2->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $response = $this->postJson('extract', [
            'query' => '{
                ClassSix(language: es)
            }'
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            [
                'key' => 'instance-one',
                'attributes' => [
                    [
                        'id' => $valueES1->id,
                        'key' => 'default-attribute',
                        'value' => $valueES1->value,
                        'attributes' => [],
                    ]
                ]
            ], [
                'key' => 'instance-two',
                'attributes' => [
                    [
                        'id' => $valueES2->id,
                        'key' => 'default-attribute',
                        'value' => $valueES2->value,
                        'attributes' => [],
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function extractMultipleInstancesSuccessfullyFromMysql(): void
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

        $valueES1 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute1->id,
            'language' => 'es',
            'value' => 'valor1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute1->id,
            'language' => 'en',
            'value' => 'value1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instance2 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-four',
            'key' => 'instance-two',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $attribute2 = AttributeDAO::create([
            'instance_id' => $instance2->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $valueES2 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute2->id,
            'language' => 'es',
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute2->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $response = $this->postJson('extract', [
            'query' => '{
                instances(key: InstanceOne, preview: false, language: es)
                instances(key:InstanceOne, preview: false, language: en)
            }'
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            [
                'key' => 'instance-one',
                'attributes' => [
                    [
                        'id' => 1,
                        'key' => 'default-attribute',
                        'value' => 'valor1',
                        'attributes' => [],
                    ]
                ]
            ], [
                'key' => 'instance-one',
                'attributes' => [
                    [
                        'id' => 2,
                        'key' => 'default-attribute',
                        'value' => 'value1',
                        'attributes' => [],
                    ]
                ]
            ]
        ]);
    }

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

        $valueES1 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute1->id,
            'language' => 'es',
            'value' => 'valor1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute1->id,
            'language' => 'en',
            'value' => 'value1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instance2 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-four',
            'key' => 'instance-two',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $attribute2 = AttributeDAO::create([
            'instance_id' => $instance2->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $valueES2 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute2->id,
            'language' => 'es',
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute2->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instance3 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-five',
            'key' => 'instance-three',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $attribute3 = AttributeDAO::create([
            'instance_id' => $instance3->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $valueES3 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute3->id,
            'language' => 'es',
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute3->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instance4 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-five',
            'key' => 'instance-four',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $attribute4 = AttributeDAO::create([
            'instance_id' => $instance4->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $valueES4 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute4->id,
            'language' => 'es',
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute4->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instance5 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-five',
            'key' => 'instance-five',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $attribute5 = AttributeDAO::create([
            'instance_id' => $instance5->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $valueES5 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute5->id,
            'language' => 'es',
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute5->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        $instance6 = InstanceDAO::create([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-six',
            'key' => 'instance-six',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ]);

        $attribute6 = AttributeDAO::create([
            'instance_id' => $instance6->id,
            'parent_id' => null,
            'key' => 'default-attribute',
        ]);

        $valueES6 = ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute6->id,
            'language' => 'es',
            'value' => 'valor2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        ValueDAO::create([
            'uuid' => $this->faker->uuid(),
            'attribute_id' => $attribute6->id,
            'language' => 'en',
            'value' => 'value2',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ]);

        RelationDAO::create([
            'key' => 'relation-key1',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance2->id,
            'order' => 0,
        ]);

        RelationDAO::create([
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance3->id,
            'order' => 0,
        ]);

        RelationDAO::create([
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance4->id,
            'order' => 1,
        ]);

        RelationDAO::create([
            'key' => 'relation-key2',
            'parent_instance_id' => $instance1->id,
            'child_instance_id' => $instance5->id,
            'order' => 2,
        ]);

        RelationDAO::create([
            'key' => 'relation-key3',
            'parent_instance_id' => $instance4->id,
            'child_instance_id' => $instance6->id,
            'order' => 0,
        ]);

        $response = $this->postJson('extract', [
            'query' => '{
                instances(key: InstanceOne, preview: false, language: es) {
                    RelationKey1(type:child, limit: 1)
                    RelationKey2(type:child, limit: 2) {
                        RelationKey3(type:child, limit: 1)
                    }
                }
            }'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'key' => 'instance-one',
            'attributes' => [
                [
                    'id' => $valueES1->id,
                    'key' => 'default-attribute',
                    'value' => $valueES1->value,
                    'attributes' => []
                ]
            ],
            'relations' => [
                'relation-key1' => [
                    [
                        'key' => 'instance-two',
                        'attributes' => [
                            [
                                'id' => $valueES2->id,
                                'key' => 'default-attribute',
                                'value' => $valueES2->value,
                                'attributes' => []
                            ]
                        ],
                        'relations' => []
                    ]
                ],
                'relation-key2' => [
                    [
                        'key' => 'instance-three',
                        'attributes' => [
                            [
                                'id' => $valueES3->id,
                                'key' => 'default-attribute',
                                'value' => $valueES3->value,
                                'attributes' => []
                            ]
                        ],
                        'relations' => [
                            'relation-key3' => []
                        ]
                    ], [
                        'key' => 'instance-four',
                        'attributes' => [
                            [
                                'id' => $valueES4->id,
                                'key' => 'default-attribute',
                                'value' => $valueES4->value,
                                'attributes' => []
                            ]
                        ],
                        'relations' => [
                            'relation-key3' => [
                                [
                                    'key' => 'instance-six',
                                    'attributes' => [
                                        [
                                            'id' => $valueES6->id,
                                            'key' => 'default-attribute',
                                            'value' => $valueES6->value,
                                            'attributes' => []
                                        ]
                                    ],
                                    'relations' => []
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}
