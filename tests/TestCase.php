<?php
namespace Tests;

use Omatech\Mapi\MapiServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [MapiServiceProvider::class];
    }
}
