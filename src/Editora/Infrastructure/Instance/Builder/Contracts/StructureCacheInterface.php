<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts;

interface StructureCacheInterface
{
    public function get(): ?array;
    public function put(array $structure): void;
}
