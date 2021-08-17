<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts;

interface StructureLoaderInterface
{
    public function load(string $classKey): array;
}
