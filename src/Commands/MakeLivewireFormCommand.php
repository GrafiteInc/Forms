<?php

namespace Grafite\Forms\Commands;

class MakeLivewireFormCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:livewire-form';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new livewire form';

    /**
         * Get the stub file for the generator.
         *
         * @return string
         */
    protected function getStub()
    {
        return __DIR__ . '/stubs/livewire-form.stub';
    }
}
