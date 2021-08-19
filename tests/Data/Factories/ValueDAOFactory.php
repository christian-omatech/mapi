<?php declare(strict_types=1);

namespace Tests\Data\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\ValueDAO;

class ValueDAOFactory extends Factory
{
    protected $model = ValueDAO::class;

    public function definition(): array
    {
        return [
            'attribute_key' => 'all-languages-attribute',
            'parent_id' => null,
            'language' => 'es',
            'value' => 'test',
            'extra_data' => json_encode([], JSON_THROW_ON_ERROR),
        ];
    }
}
