<?php declare(strict_types=1);

namespace Omatech\Mapi;

use Illuminate\Contracts\Debug\ExceptionHandler as LaravelExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Omatech\Mapi\Editora\EditoraServiceProvider;
use Omatech\Mapi\Shared\Infrastructure\Exceptions\ExceptionHandler;
use Omatech\Mapi\Shared\Infrastructure\Providers\ConfigurationServiceProvider;

final class MapiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(LaravelExceptionHandler::class, ExceptionHandler::class);

        $this->app->register(EditoraServiceProvider::class);
        $this->app->register(ConfigurationServiceProvider::class);

        $this->loadMigrationsFrom(__DIR__.'/Shared/Infrastructure/Persistence/Eloquent/Migrations');
    }
}
