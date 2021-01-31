<?php

namespace Grafite\Forms\Commands;

class MakeModalFormCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:modal-form';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new modal form';

    /**
         * Get the stub file for the generator.
         *
         * @return string
         */
    protected function getStub()
    {
        return __DIR__ . '/stubs/modal-form.php';
    }
}
