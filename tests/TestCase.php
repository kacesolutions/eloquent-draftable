<?php

namespace Kace\EloquentDraftable\Tests;

use Orchestra\Testbench\TestCase as BaseTest;

abstract class TestCase extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->withFactories(__DIR__.'/database/factories');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
