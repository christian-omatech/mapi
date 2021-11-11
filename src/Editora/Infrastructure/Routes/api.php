<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\CreateInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\DeleteInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\EditoraController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\ExtractInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\ReadInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\UpdateInstanceController;

Route::middleware('jsonRequest')->group(function ($route) {
    $route->post('extract', ExtractInstanceController::class);
    $route->post('/', CreateInstanceController::class);
    $route->get('{uuid}', ReadInstanceController::class);
    $route->put('{uuid}', UpdateInstanceController::class);
    $route->delete('{uuid}', DeleteInstanceController::class);
});

Route::get('{language}/{niceUrl}/products/{uuid}', EditoraController::class);