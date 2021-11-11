<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Attribute;

use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Contracts\UniqueValueInterface;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;

final class AttributeRepository implements UniqueValueInterface
{
    public function isUnique(BaseValue $value): bool
    {
        return ! AttributeDAO::where('key', $value->key())
            ->whereHas('values', function ($q) use ($value) {
                $q->where('value', $value->value())
                    ->whereNotNull('value')
                    ->where('uuid', '<>', $value->uuid());
            })->exists();
    }
}
