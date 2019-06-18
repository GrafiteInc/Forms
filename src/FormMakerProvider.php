<?php

namespace Grafite\FormMaker;

use Illuminate\Support\ServiceProvider;
use Grafite\FormMaker\Commands\MakeFormCommand;
use Grafite\FormMaker\Commands\MakeFieldCommand;

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
            __DIR__.'/../config/form-maker.php' => base_path('config/form-maker.php'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Commands
        |--------------------------------------------------------------------------
        */

        $this->commands([
            MakeFieldCommand::class,
            MakeFormCommand::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
