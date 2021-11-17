<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora;

use Illuminate\Support\ServiceProvider;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureCacheInterface;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureLoaderInterface;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Cache\ExtractionCache;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Cache\InstanceBuilderCache;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Cache\StructureCache;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Attribute\AttributeRepository;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance\ExtractionRepository;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance\InstanceRepository;
use Omatech\Mapi\Shared\Infrastructure\Http\Middleware\JsonRequest;
use Omatech\Mcore\Editora\Domain\Attribute\Contracts\AttributeRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Contracts\ExtractionCacheInterface;

final class EditoraServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(InstanceRepositoryInterface::class, InstanceRepository::class);
        $this->app->bind(AttributeRepositoryInterface::class, AttributeRepository::class);
        $this->app->bind(ExtractionRepositoryInterface::class, ExtractionRepository::class);
        $this->app->bind(InstanceCacheInterface::class, InstanceBuilderCache::class);
        $this->app->bind(ExtractionCacheInterface::class, ExtractionCache::class);
        $this->app->bind(StructureCacheInterface::class, StructureCache::class);

        $this->app->bind(StructureLoaderInterface::class, config('mage.editora.structure_loader'));

        $this->app['router']->aliasMiddleware('jsonRequest', JsonRequest::class);

        $this->loadRoutesFrom(__DIR__.'/Infrastructure/Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/Infrastructure/Persistence/Eloquent/Migrations');
    }
}
