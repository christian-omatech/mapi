<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Attribute;

use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\AttributeDAO;
use Omatech\Mcore\Editora\Domain\Attribute\Contracts\AttributeRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Contracts\UniqueValueInterface;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;

final class AttributeRepository implements AttributeRepositoryInterface, UniqueValueInterface
{
    public function classKeyWithAlternateNiceUrls(string $niceUrl): array
    {
        $attribute = AttributeDAO::where('key', 'niceUrl')
            ->with('instance', 'values')
            ->whereHas('values', function($q) use ($niceUrl) {
                $q->where('value', $niceUrl)
                    ->whereNotNull('value');
            })->first();
        return [
            'key' => $attribute->instance->class_key,
            'niceUrls' => $attribute->values->reduce(function($acc, $value) {
                $acc[$value->language] = $value->value;
                return $acc;
            }, [])
        ];
    }

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
