<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Tactician;

use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator as HandlerLocatorInterface;

class HandlerLocator implements HandlerLocatorInterface
{
    public function getHandlerForCommand($commandName)
    {
        $handler = app($commandName . 'Handler');
        return $handler ?? throw MissingHandlerException::forCommand($commandName);
    }
}
