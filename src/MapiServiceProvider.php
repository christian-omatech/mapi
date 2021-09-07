<?php declare(strict_types=1);

namespace Omatech\Mapi;

use Illuminate\Contracts\Debug\ExceptionHandler as LaravelExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Omatech\Mapi\Editora\EditoraServiceProvider;
use Omatech\Mapi\Shared\Infrastructure\Events\EventPublisher;
use Omatech\Mapi\Shared\Infrastructure\Exceptions\ExceptionHandler;
use Omatech\Mapi\Shared\Infrastructure\Providers\ConfigurationServiceProvider;
use Omatech\Mapi\Shared\Infrastructure\Providers\EventServiceProvider;
use Omatech\Mcore\Shared\Domain\Event\Contracts\EventPublisherInterface;

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
        $this->app->register(EditoraServiceProvider::class);
        $this->app->register(ConfigurationServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        $this->app->bind(LaravelExceptionHandler::class, ExceptionHandler::class);
        $this->app->bind(EventPublisherInterface::class, EventPublisher::class);

        $this->loadMigrationsFrom(__DIR__.'/Shared/Infrastructure/Persistence/Eloquent/Migrations');
    }
}
