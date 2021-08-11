<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class JsonRequest
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
