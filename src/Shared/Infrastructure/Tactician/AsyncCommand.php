<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Tactician;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Omatech\Mcore\Shared\Application\Command;

final class AsyncCommand implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Command $command;

    public function __construct(Command $command, string $queue)
    {
        $this->command = $command;
        $this->queue = $queue;
    }

    public function handle(): void
    {
        (new CommandBus())->handle($this->command);
    }
}
