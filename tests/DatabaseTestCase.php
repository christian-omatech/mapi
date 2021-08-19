<?php declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class DatabaseTestCase extends TestCase
{
    use DatabaseMigrations;

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set(
            'mage.editora.structure_path',
            __DIR__.'/Data/data.yml'
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Tests\\Data\\Factories\\'.class_basename($modelName).'Factory';
        });
    }
}
