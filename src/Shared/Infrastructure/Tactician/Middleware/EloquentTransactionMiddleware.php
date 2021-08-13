<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Tactician\Middleware;

use Exception;
use Illuminate\Support\Facades\DB;
use League\Tactician\Middleware;

class EloquentTransactionMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        try {
            DB::beginTransaction();
            $output = $next($command);
            DB::commit();
            return $output;
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
}
