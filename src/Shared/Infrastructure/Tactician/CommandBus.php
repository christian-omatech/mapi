<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Tactician;

use Omatech\Mcore\Shared\Application\Command;

final class CommandBus extends Bus
{
    public function __construct()
    {
        parent::__construct('command_middleware');
    }

    public function handleAsync(Command $command, string $queue): void
    {
        dispatch(new AsyncCommand($command, $queue));
    }
}
