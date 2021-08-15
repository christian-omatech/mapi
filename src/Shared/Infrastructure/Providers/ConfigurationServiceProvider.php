<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

final class ConfigurationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 4).'/config/mage.php',
            'mage'
        );
    }
}
