<?php

namespace Grafite\Forms;

use Illuminate\Support\Str;
use Grafite\Forms\Components\Form;
use Grafite\Forms\Components\FormBase;
use Grafite\Forms\Services\FormAssets;
use Grafite\Forms\Components\FormModal;
use Grafite\Forms\Components\FormModel;
use Illuminate\Support\ServiceProvider;
use Grafite\Forms\Components\FormAction;
use Grafite\Forms\Components\FormDelete;
use Grafite\Forms\Components\FormSearch;
use Grafite\Forms\Commands\MakeFieldCommand;
use Grafite\Forms\Commands\MakeBaseFormCommand;
use Grafite\Forms\Commands\MakeModalFormCommand;
use Grafite\Forms\Commands\MakeModelFormCommand;
use Grafite\Forms\Commands\MakeWizardFormCommand;
use Grafite\Forms\Commands\MakeLivewireFormCommand;

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

        $this->app['blade.compiler']->directive('forms', function ($nonce) {
            return "<?php echo app('" . FormAssets::class . "')->render('all', {$nonce}); ?>";
        });

        $this->app['blade.compiler']->directive('formScripts', function ($nonce) {
            return "<?php echo app('" . FormAssets::class . "')->render('scripts', {$nonce}); ?>";
        });

        $this->app['blade.compiler']->directive('formStyles', function ($nonce) {
            return "<?php echo app('" . FormAssets::class . "')->render('styles', {$nonce}); ?>";
        });

        $this->app['blade.compiler']->component('f', Form::class);
        $this->app['blade.compiler']->component('f-base', FormBase::class);
        $this->app['blade.compiler']->component('f-model', FormModel::class);
        $this->app['blade.compiler']->component('f-action', FormAction::class);
        $this->app['blade.compiler']->component('f-modal', FormModal::class);
        $this->app['blade.compiler']->component('f-delete', FormDelete::class);
        $this->app['blade.compiler']->component('f-search', FormSearch::class);

        $this->commands([
            MakeFieldCommand::class,
            MakeModelFormCommand::class,
            MakeModalFormCommand::class,
            MakeBaseFormCommand::class,
            MakeLivewireFormCommand::class,
            MakeWizardFormCommand::class,
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
