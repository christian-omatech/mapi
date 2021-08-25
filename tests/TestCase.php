<?php declare(strict_types=1);

namespace Tests;

use Omatech\Mapi\MapiServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set(
            'mage.editora.structure_path',
            __DIR__.'/Data/data.yml'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [MapiServiceProvider::class];
    }
}
