<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora;

use Illuminate\Support\ServiceProvider;
use Omatech\Mapi\Shared\Infrastructure\Http\Middleware\JsonRequest;

final class EditoraServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('jsonRequest', JsonRequest::class);

        $this->loadRoutesFrom(__DIR__.'/Infrastructure/Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/Infrastructure/Persistence/Eloquent/Migrations');
    }
}
