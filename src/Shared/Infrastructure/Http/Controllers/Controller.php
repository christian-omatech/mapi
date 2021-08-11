<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Omatech\Mapi\Shared\Infrastructure\Tactician\Bus;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected Bus $commandBus;
    protected Bus $queryBus;

    public function __construct()
    {
        $this->commandBus = new Bus(Bus::COMMAND_BUS);
        $this->queryBus = new Bus(Bus::QUERY_BUS);
    }
}
