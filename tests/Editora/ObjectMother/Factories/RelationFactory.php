<?php
namespace Tests\Editora\ObjectMother\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\RelationDAO;

class RelationFactory extends Factory
{
    protected $model = RelationDAO::class;

    public function definition()
    {
        return [            
            'key' => 'country-home',
            'order' => 0,
        ];
    }
}