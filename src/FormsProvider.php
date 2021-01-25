<?php

namespace Grafite\Forms;

use Grafite\Forms\Components\Form;
use Grafite\Forms\Services\FormAssets;
use Grafite\Forms\Components\FormModal;
use Illuminate\Support\ServiceProvider;
use Grafite\Forms\Components\FormAction;
use Grafite\Forms\Components\FormDelete;
use Grafite\Forms\Components\FormSearch;
use Grafite\Forms\Commands\MakeFieldCommand;
use Grafite\Forms\Commands\MakeBaseFormCommand;
use Grafite\Forms\Commands\MakeFormTestCommand;
use Grafite\Forms\Commands\MakeModelFormCommand;
use Grafite\Forms\Commands\MakeFormFactoryCommand;

class FormsProvider extends ServiceProvider
{
    /**
     * Boot method.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/forms.php' => base_path('config/forms.php'),
        ]);

        $this->app['blade.compiler']->directive('forms', function () {
            return "<?php echo app('" . FormAssets::class . "')->render(); ?>";
        });

        $this->app['blade.compiler']->component('f', Form::class);
        $this->app['blade.compiler']->component('f-action', FormAction::class);
        $this->app['blade.compiler']->component('f-modal', FormModal::class);
        $this->app['blade.compiler']->component('f-delete', FormDelete::class);
        $this->app['blade.compiler']->component('f-search', FormSearch::class);

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
