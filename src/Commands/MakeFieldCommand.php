<?php

namespace Grafite\FormMaker\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class MakeFieldCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:field {field}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new field';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $field = $this->argument('field');

        $fileName = ucfirst($field).'.php';

        if (!is_dir(app_path('Http/Forms/Fields'))) {
            mkdir(app_path('Http/Forms/Fields'));
        }

        $file = app_path('Http/Forms/Fields/'.$fileName);
        $stub = __DIR__.'/stubs/field.php';

        $contents = file_get_contents($stub);

        $contents = str_replace('{field}', $field, $contents);

        if (!file_exists($file)) {
            file_put_contents(app_path('Http/Forms/Fields/'.$fileName), $contents);
        }

        $this->info('You have a form for '.$field.' field.');
    }
}
