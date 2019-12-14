<?php

namespace Grafite\FormMaker\Commands;

use Illuminate\Console\Command;

class MakeFormFactoryCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:form-factory {form}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a factory based on a form';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $form = $this->argument('form');

        $form = app($form);
        $fields = $form->factoryFields();

        $fileName = ucfirst($form->model).'Factory.php';

        if (!is_dir(base_path('database'))) {
            mkdir(base_path('database'));
        }

        if (!is_dir(base_path('database/factories/'))) {
            mkdir(base_path('database/factories/'));
        }

        $file = base_path('database/factories/'.$fileName);
        $stub = __DIR__.'/stubs/factory.php';

        $contents = file_get_contents($stub);

        $contents = str_replace('DummyClass', get_class($form->modelClass).'::class', $contents);
        $contents = str_replace('DummyFields', $fields, $contents);

        if (!file_exists($file)) {
            file_put_contents(base_path('database/factories/'.$fileName), $contents);
        }

        $this->info('You now have a factory for '.$form->model);
    }
}
