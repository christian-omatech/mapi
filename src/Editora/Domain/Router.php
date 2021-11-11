<?php

namespace Omatech\Mapi\Editora\Domain;

use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use function Lambdish\Phunctional\flatten;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\search;
use function Lambdish\Phunctional\first;

class Router
{
    /** @var array<Route> $routes  */
    private array $routes;

    public function __construct(array $configuration)
    {
        $this->routes = reduce(function(array $acc, array $route) use ($configuration): array {
            return array_merge($acc, $this->prepareRoutes($route, $configuration));
        }, $configuration['router'], []);
    }

    private function prepareRoutes(array $route, array $configuration): array
    {
        if(isset($route['translate']) && $route['translate']) {
             return reduce(function(array $acc, string $language) use ($route) {
                $segments = $this->translateSegments($language, $route['segments']);
                $acc[] = new Route(array_replace($route, ['segments' => $segments]));
                return $acc;
            }, $configuration['languages'], []);
        }
        return [new Route($route)];
    }

    private function translateSegments(string $language, array $segments): array
    {
        $segmentsToTranslate = filter(function(string $segment) : bool {
            return !strpos($segment, '{') && !strpos($segment, '}');
        }, $segments);
        $translatedSegments = map(function(string $segment) use ($language) : string {
            return trans('mage.editora.router.segment.'.$segment, [], $language);
        }, $segmentsToTranslate);
        $languageSegment = filter(fn($segment) => $segment === '{language}', $segments);
        if($languageSegment) {
            $languageSegment[key($languageSegment)] = $language;
        }
        return array_replace($segments, $translatedSegments, $languageSegment);
    }

    public function findController(string $uri, string $class): string
    {
        $route = search(fn(Route $route): bool => $route->hash() === md5($uri), $this->routes);
        $route->ensureClassIsAllowed($class);
        return $route->controller($class);
    }

    public function routes()
    {
        return $this->routes;
    }
}