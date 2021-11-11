<?php

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Omatech\Mapi\Editora\Domain\Route;
use Omatech\Mapi\Editora\Domain\Router;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use function Lambdish\Phunctional\reduce;

final class EditoraController extends Controller
{
    public function __invoke(Request $request)
    {
        //find class by niceurl
        $class = 'Products';
        $router = new Router(config('mage.editora'));
        $controller = app()->make($router->findController($request->route()->uri(), $class));
        $request->route()->controller = $controller;
        $request->route()->action['uses'] = $controller::class.'@__invoke';
        $request->route()->action['controller'] = $controller::class;
        $request->route()->computedMiddleware = array_merge(reduce(function(array $acc, array $middleware): array {
            $acc[] = $middleware['middleware'];
            return $acc;
        }, $controller->getMiddleware(), []), $request->route()->computedMiddleware);
        return app()->make(Kernel::class)->handle($request);
    }
}
