<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Cache;

use Illuminate\Support\Facades\Cache;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;

final class InstanceBuilderCache implements InstanceCacheInterface
{
    public function get(string $hash): ?Instance
    {
        return Cache::get($hash);
    }

    public function put(string $hash, Instance $extraction): void
    {
        Cache::put($hash, $extraction);
    }
}
