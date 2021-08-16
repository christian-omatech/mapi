<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\CreateInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\DeleteInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\ReadInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\UpdateInstanceController;

Route::middleware('jsonRequest')->group(function ($route) {
    $route->post('/', CreateInstanceController::class);
    $route->get('{id}', ReadInstanceController::class)->where('id', '[0-9]+');
    $route->put('{id}', UpdateInstanceController::class)->where('id', '[0-9]+');
    $route->delete('{id}', DeleteInstanceController::class)->where('id', '[0-9]+');
});
