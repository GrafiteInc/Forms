<?php

class AppTest extends Orchestra\Testbench\TestCase
{
    protected $app;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app->make('Illuminate\Contracts\Http\Kernel');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Yab\FormMaker\FormMakerProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Form'       => \Collective\Html\FormFacade::class,
            'HTML'       => \Collective\Html\HtmlFacade::class,
            'FormMaker'  => \Yab\FormMaker\Facades\FormMaker::class,
            'InputMaker' => \Yab\FormMaker\Facades\InputMaker::class,
        ];
    }

    public function setUp()
    {
        parent::setUp();
        $this->withFactories(__DIR__.'/../src/Models/Factories');
        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/../src/Migrations'),
        ]);
        $this->withoutMiddleware();
        $this->withoutEvents();
    }

    public function testFormMaker()
    {
        $formMaker = $this->app['FormMaker'];
        $this->assertTrue(is_object($formMaker));
    }

    public function testInputMaker()
    {
        $inputMaker = $this->app['InputMaker'];
        $this->assertTrue(is_object($inputMaker));
    }
}
