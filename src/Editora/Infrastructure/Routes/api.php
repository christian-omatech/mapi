<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\CreateInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\DeleteInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\ReadInstanceController;
use Omatech\Mapi\Editora\Infrastructure\Http\Controllers\UpdateInstanceController;

Route::middleware('jsonRequest')->post('/', CreateInstanceController::class);
Route::get('{id}', ReadInstanceController::class);
Route::put('{id}', UpdateInstanceController::class);
Route::delete('{id}', DeleteInstanceController::class);
