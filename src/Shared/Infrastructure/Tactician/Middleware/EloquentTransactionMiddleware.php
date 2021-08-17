<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Tactician\Middleware;

use Illuminate\Support\Facades\DB;
use League\Tactician\Middleware;

class EloquentTransactionMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        DB::transaction(function () use ($command, $next) {
            /** @infection-ignore-all */
            return $next($command);
        });
    }
}
