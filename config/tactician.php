<?php declare(strict_types=1);

use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use Omatech\Mapi\Shared\Infrastructure\Tactician\HandlerLocator;

return [
    'command_name_extractor' => ClassNameExtractor::class,
    'handler_locator' => HandlerLocator::class,
    'method_name_inflector' => InvokeInflector::class,

    /**
     * List of middleware to apply to the CommandBus
     * MUST be in proper loading order from top to bottom
     * To add a middleware simply use a descriptive key and the class name as the value
     */
    'command_middleware' => [
    ],
    'query_middleware' => [
    ],
];
