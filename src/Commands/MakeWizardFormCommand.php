<?php

namespace Grafite\Forms\Commands;

class MakeWizardFormCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:wizard-form';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new wizard form';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/wizard-form.stub';
    }
}
