<?php
namespace Tests\Editora\ObjectMother\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;

class ValueFactory extends Factory
{
    protected $model = ValueDAO::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'language' => 'es',
            'value' => 'valor1',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ];
    }
}