<?php

namespace Omatech\Mapi\Editora\Domain;

use Omatech\Mapi\Editora\Domain\Exceptions\NotAllowedClassException;

final class Route
{
    private string $controllerNamespace;
    private array $classes;
    private array $segments;
    private bool $translate;
    private string $hash;

    public function __construct(array $route)
    {
        $this->controllerNamespace = $route['controller_namespace'];
        $this->classes = $route['classes'] ?? ['*'];
        $this->segments = $route['segments'] ?? [];
        $this->translate = $route['translate'] ?? false;
        $this->hash = md5(implode('/', $this->segments));
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function ensureClassIsAllowed(string $class): void
    {
        $isAllowed = !in_array('*', $this->classes, true) &&
            in_array(lcfirst($class), $this->classes, true);
        if(!$isAllowed) {
            throw new NotAllowedClassException();
        }
    }

    public function controller(string $class): string
    {
        return $this->controllerNamespace.'\\'.$class.'Controller';
    }

    public function segments(): string
    {
        return implode('/', $this->segments);
    }
}