<?php
namespace Tests\Editora\ObjectMother\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;

class InstanceFactory extends Factory
{
    protected $model = InstanceDAO::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'class_key' => 'home',
            'key' => 'home-es',
            'status' => 'in-revision',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => '2100-03-08 09:00:00',
        ];
    }
}