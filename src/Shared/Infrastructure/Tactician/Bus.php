<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Tactician;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use function Lambdish\Phunctional\each;

class Bus extends CommandBus
{
    public const QUERY_BUS = 'query_middleware';
    public const COMMAND_BUS = 'command_middleware';

    private array $middleware;

    public function __construct(string $type)
    {
        $this->loadConfigMiddleware($type);
        $this->addCommandHandlerMiddleware();
        parent::__construct($this->middleware);
    }

    private function loadConfigMiddleware(string $type): void
    {
        each(function (string $middleware) {
            $this->middleware[] = app($middleware);
        }, config('mage.commandbus')[$type]);
    }

    private function addCommandHandlerMiddleware(): void
    {
        $this->middleware[] = new CommandHandlerMiddleware(
            app(config('mage.commandbus')['command_name_extractor']),
            app(config('mage.commandbus')['handler_locator']),
            app(config('mage.commandbus')['method_name_inflector'])
        );
    }
}
