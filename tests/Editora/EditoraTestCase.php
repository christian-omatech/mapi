<?php

namespace Tests\Editora;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditoraTestCase extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set(
            'mage.editora.structure_path',
            __DIR__.'/Data/structure.yml'
        );
    }
}