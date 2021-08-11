<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Tactician;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;

class Bus extends CommandBus
{
    public const QUERY_BUS = 'query_middleware';
    public const COMMAND_BUS = 'command_middleware';

    private array $middleware;
    private array $config;

    public function __construct(string $type)
    {
        $this->config = config('tactician');
        $this->loadConfigMiddleware($type);
        $this->addCommandHandlerMiddleware();
        parent::__construct($this->middleware);
    }

    private function loadConfigMiddleware(string $type): void
    {
        foreach ($this->config[$type] as $middleware) {
            $this->middleware[] = app($middleware);
        }
    }

    private function addCommandHandlerMiddleware(): void
    {
        $this->middleware[] = new CommandHandlerMiddleware(
            app($this->config['command_name_extractor']),
            app($this->config['handler_locator']),
            app($this->config['method_name_inflector'])
        );
    }
}
