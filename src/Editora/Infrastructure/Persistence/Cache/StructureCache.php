<?php

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Cache;

use Illuminate\Support\Facades\Cache;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureCacheInterface;

final class StructureCache implements StructureCacheInterface
{
    private const STRUCTURE_KEY = 'mage.editora.structure';

    public function get(): ?array
    {
        return Cache::get($this::STRUCTURE_KEY);
    }

    public function put(array $structure): void
    {
        Cache::put($this::STRUCTURE_KEY, $structure);
    }
}
