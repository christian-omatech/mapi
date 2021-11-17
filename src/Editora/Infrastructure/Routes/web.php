<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\EditoraController;
use Omatech\Mcore\Editora\Domain\Router\Router;

$router = Router::instance(config('mage.editora.router'), config('mage.editora.languages'));
foreach ($router->routes() as $route) {
    Route::get($route->uri(), EditoraController::class);
}
