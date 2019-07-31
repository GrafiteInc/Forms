<?php

namespace Grafite\FormMaker\Commands;

use Illuminate\Console\Command;

class MakeBaseFormCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:base-form {entity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new base form';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $entity = $this->argument('entity');

        $fileName = ucfirst($entity).'Form.php';
        $file = app_path('Http/Forms/'.$fileName);
        $stub = __DIR__.'/stubs/baseform.php';

        $contents = file_get_contents($stub);

        $contents = str_replace('{form}', $entity.'Form', $contents);

        if (!file_exists($file)) {
            file_put_contents(app_path('Http/Forms/'.$fileName), $contents);
        }

        $this->info('You have a base form for '.$entity);
    }
}
