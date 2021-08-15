<?php
namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Omatech\Mapi\MapiServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [MapiServiceProvider::class];
    }
}
