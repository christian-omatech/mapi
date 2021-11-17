<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Omatech\Mapi\Shared\Infrastructure\Tactician\CommandBus;
use Omatech\Mapi\Shared\Infrastructure\Tactician\QueryBus;
use function Lambdish\Phunctional\reduce;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected CommandBus $commandBus;
    protected QueryBus $queryBus;

    public function __construct()
    {
        $this->commandBus = new CommandBus();
        $this->queryBus = new QueryBus();
    }
    
    protected function middlewares(): array
    {
        return reduce(function($acc, $middleware) {
            $acc[] = $middleware['middleware'];
            return $acc;
        }, $this->middleware);
    }
}
