<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora;

use Illuminate\Support\ServiceProvider;
use Omatech\Mapi\Editora\Infrastructure\Instance\StructureLoaderInterface;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\InstanceRepository;
use Omatech\Mapi\Shared\Infrastructure\Http\Middleware\JsonRequest;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;

final class EditoraServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(InstanceRepositoryInterface::class, InstanceRepository::class);
        $this->app->bind(StructureLoaderInterface::class, config('mage.editora.structure_loader'));

        $this->app['router']->aliasMiddleware('jsonRequest', JsonRequest::class);

        $this->loadRoutesFrom(__DIR__.'/Infrastructure/Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/Infrastructure/Persistence/Eloquent/Migrations');
    }
}
