<?php

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Cache;

use Illuminate\Support\Facades\Cache;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Contracts\ExtractionCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Extraction;

final class ExtractionCache implements ExtractionCacheInterface
{
    public function get(string $hash): ?Extraction
    {
        return Cache::get($hash);
    }

    public function put(string $hash, Extraction $extraction): void
    {
        Cache::put($hash, $extraction);
    }
}
