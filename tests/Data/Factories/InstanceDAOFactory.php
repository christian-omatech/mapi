<?php

namespace Tests\Data\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;

class InstanceDAOFactory extends Factory
{
    protected $model = InstanceDAO::class;
    
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'class_key' => 'class-one',
            'key' => 'instance-test',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
        ];
    }
}
