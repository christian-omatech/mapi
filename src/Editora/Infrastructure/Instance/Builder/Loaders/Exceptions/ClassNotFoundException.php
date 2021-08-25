<?php

namespace Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Loaders\Exceptions;

use Exception;

final class ClassNotFoundException extends Exception
{
    public static function withClass(string $classKey): self
    {
        throw new self("Class {$classKey} not found.");
    }
}