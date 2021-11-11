<?php

namespace Tests\Editora\ObjectMother;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Editora\ObjectMother\Factories\AttributeFactory;
use Tests\Editora\ObjectMother\Factories\InstanceFactory;
use Tests\Editora\ObjectMother\Factories\ValueFactory;
use Faker\Factory;
use Faker\Generator;

class NewsMother
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function database()
    {
        return InstanceFactory::new()
            ->has(AttributeFactory::new()
                ->state(['key' => 'title'])
                ->has(ValueFactory::new()
                    ->state([
                        'uuid' => $this->faker->uuid(),
                        'language' => 'es',
                        'value' => 'title-es',
                    ], [
                        'uuid' => $this->faker->uuid(),
                        'language' => 'en',
                        'value' => 'title-en',
                    ]), 
                'values'),
            'attributes')
            ->has(AttributeFactory::new()
                ->state(['key' => 'description'])
                ->has(ValueFactory::new()
                    ->state([
                        'uuid' => $this->faker->uuid(),
                        'language' => 'es',
                        'value' => 'description-es',
                    ], [
                        'uuid' => $this->faker->uuid(),
                        'language' => 'en',
                        'value' => 'description-en',
                    ]), 
                'values'),
            'attributes')
            ->has(AttributeFactory::new()
                ->state(['key' => 'nice-url'])
                ->has(ValueFactory::new()
                    ->state([
                        'uuid' => $this->faker->uuid(),
                        'language' => 'es',
                        'value' => '/es/soy-una-url',
                    ], [
                        'uuid' => $this->faker->uuid(),
                        'language' => 'en',
                        'value' => '/en/soy-una-url',
                    ]), 
                'values'),
            'attributes')
        ->createOne([
            'uuid' => $this->faker->uuid(),
            'class_key' => 'news', 
            'key' => 'new-instance'
        ]);    
    }
}