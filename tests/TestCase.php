<?php
namespace Tests;

use Illuminate\Support\Facades\File;
use Omatech\Mapi\MapiServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [MapiServiceProvider::class];
    }
}
