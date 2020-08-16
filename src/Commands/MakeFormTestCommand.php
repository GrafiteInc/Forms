<?php

namespace Grafite\Forms\Commands;

use Illuminate\Console\Command;

class MakeFormTestCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:form-test {form}';

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

        $fileName = ucfirst($form->model) . 'Test.php';

        if (!is_dir(base_path('tests'))) {
            mkdir(base_path('tests'));
        }

        if (!is_dir(base_path('tests/Feature/'))) {
            mkdir(base_path('tests/Feature/'));
        }

        $file = base_path('tests/Feature/' . $fileName);
        $stub = __DIR__ . '/stubs/test.php';

        $contents = file_get_contents($stub);

        $contents = str_replace('DummyTest', ucfirst($form->model) . 'Test', $contents);
        $contents = str_replace('DummyModel', get_class($form->modelClass), $contents);
        $contents = str_replace('DummyRoutePrefix', $form->routePrefix, $contents);
        $contents = str_replace('DummyRouteCreate', $form->routes['create'], $contents);
        $contents = str_replace('DummyRouteUpdate', $form->routes['update'], $contents);
        $contents = str_replace('DummyRouteDelete', $form->routes['delete'], $contents);

        if (!file_exists($file)) {
            file_put_contents(base_path('tests/Feature/' . $fileName), $contents);
        }

        $this->info('You now have a test for ' . $form->model);
    }
}
