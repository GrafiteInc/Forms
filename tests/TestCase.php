<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class TestCase extends Orchestra\Testbench\TestCase
{
    protected $app;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app->make('Illuminate\Contracts\Http\Kernel');

        View::addLocation(__DIR__.'/fixtures/views');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Grafite\Forms\FormsProvider::class,
        ];
    }

    public function setUp(): void
    {
        parent::setUp();

        $destinationDir = realpath(__DIR__.'/../vendor/orchestra/testbench-core/laravel/database/migrations');
        File::copyDirectory(realpath(__DIR__.'/migrations'), $destinationDir);

        $this->artisan('migrate', [
            '--database' => 'testbench',
        ]);

        $this->withoutMiddleware();
        $this->withoutEvents();
    }
}
