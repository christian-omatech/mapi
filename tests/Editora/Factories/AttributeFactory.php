<?php
namespace Tests\Editora\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;

class AttributeFactory extends Factory
{
    protected $model = AttributeDAO::class;

    public function definition()
    {
        return [
            'parent_id' => null,
            'key' => 'nice-url',
        ];
    }
}