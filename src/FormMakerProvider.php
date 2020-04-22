<?php

namespace Grafite\FormMaker;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Grafite\FormMaker\Services\FormAssets;
use Grafite\FormMaker\Commands\MakeFieldCommand;
use Grafite\FormMaker\Commands\MakeBaseFormCommand;
use Grafite\FormMaker\Commands\MakeFormTestCommand;
use Grafite\FormMaker\Commands\MakeModelFormCommand;
use Grafite\FormMaker\Commands\MakeFormFactoryCommand;

class FormMakerProvider extends ServiceProvider
{
    /**
     * Boot method.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/form-maker.php' => base_path('config/form-maker.php'),
        ]);

        $this->app['blade.compiler']->directive('formMaker', function () {
            return "<?php echo app('" . FormAssets::class . "')->render(); ?>";
        });

        /*
        |--------------------------------------------------------------------------
        | Commands
        |--------------------------------------------------------------------------
        */

        $this->commands([
            MakeFieldCommand::class,
            MakeModelFormCommand::class,
            MakeBaseFormCommand::class,
            MakeFormFactoryCommand::class,
            MakeFormTestCommand::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FormAssets::class, function ($app) {
            return new FormAssets($app);
        });
    }
}
