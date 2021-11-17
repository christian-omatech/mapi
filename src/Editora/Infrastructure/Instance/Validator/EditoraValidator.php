<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Instance\Validator;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\InstanceBuilder;
use Omatech\Mcore\Editora\Domain\Attribute\Attribute;
use Omatech\Mcore\Editora\Domain\Value\BaseValue;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class EditoraValidator
{
    private InstanceBuilder $builder;

    public function __construct(InstanceBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function create(array $input): array
    {
        $instance = $this->builder->build($input['classKey'] ?? '');
        $attributes = $this->parseAttributes(
            $instance->attributes()->get(),
            $input['attributes'] ?? []
        );
        return [
            'uuid' => 'required|string|uuid',
            'classKey' => 'required|string',
            'key' => 'required|string',
            'status' => 'required|string',
            'startPublishingDate' => 'required|date_format:Y-m-d H:i:s',
            'endPublishingDate' => 'nullable|date_format:Y-m-d H:i:s',
            'attributes' => 'array',
            'relations' => 'array',
        ] + $this->cleanRules(Arr::dot(['attributes' => $attributes]));
    }

    /** @param array<Attribute> $attributes */
    private function parseAttributes(array $attributes, array $input = []): array
    {
        return reduce(function (array $acc, Attribute $attribute) use ($input): array {
            $acc[$attribute->key()]['values'] = $this->parseValues(
                $attribute->values()->get(),
                $input[$attribute->key()]['values'] ?? []
            );
            $acc[$attribute->key()]['attributes'] = $this->parseAttributes(
                $attribute->attributes()->get(),
                $input[$attribute->key()]['attributes'] ?? []
            );
            return $acc;
        }, $attributes, []);
    }

    /** @param array<BaseValue> $values */
    private function parseValues(array $values, array $input = []): array
    {
        return reduce(function (array $acc, BaseValue $value) use ($input): array {
            $input = search(fn ($input) => $input['language'] === $value->language(), $input);
            $value->fill(['uuid' => $input['uuid'] ?? null, 'value' => 'noValue']);
            $acc[] = $this->parseRules($value);
            return $acc;
        }, $values, []);
    }

    private function parseRules(BaseValue $value): array
    {
        $rules = reduce(function (array $acc, mixed $conditions, string $rule) use ($value): array {
            return array_merge($acc, [$this->matchRule($rule, $conditions, $value)]);
        }, $value->rules(), []);

        return [
            'language' => search(static fn (mixed $rule) => $rule === 'required', $rules, ''),
            'value' => implode('|', $rules),
        ];
    }

    private function matchRule(string $rule, mixed $conditions, BaseValue $baseValue): mixed
    {
        $rules = [
            'required' => 'required',
            'unique' => Rule::unique('mage_values', 'value')->ignore($baseValue->uuid(), 'uuid'),
        ];
        return $rules[$rule];
    }

    private function cleanRules($rules): array
    {
        return filter(fn (mixed $rule) => $rule, $rules);
    }
}
