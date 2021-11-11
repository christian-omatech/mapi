<?php

use Illuminate\Support\Facades\Route;
use Omatech\Mapi\Editora\Domain\Router;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\EditoraController;

$router = new Router(config('mage.editora'));

foreach($router->routes() as $route) {
    Route::get($route->segments(), EditoraController::class);
}