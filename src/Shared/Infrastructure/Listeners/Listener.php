<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Listeners;

use Omatech\Mapi\Shared\Infrastructure\Tactician\CommandBus;
use Omatech\Mapi\Shared\Infrastructure\Tactician\QueryBus;
use Omatech\Mcore\Shared\Domain\Event\Event;

abstract class Listener
{
    protected CommandBus $commandBus;
    protected QueryBus $queryBus;

    public function __construct()
    {
        $this->commandBus = new CommandBus();
        $this->queryBus = new QueryBus();
    }

    abstract public function handle(Event $event): void;
}
