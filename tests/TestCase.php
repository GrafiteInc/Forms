<?php

use Illuminate\Support\Facades\File;

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
    }

    protected function getPackageProviders($app)
    {
        return [
            \Grafite\FormMaker\FormMakerProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'FormMaker' => \Grafite\FormMaker\Facades\FormMaker::class,
            'InputMaker' => \Grafite\FormMaker\Facades\InputMaker::class,
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
