<?php

namespace Grafite\Forms\Commands;

use Illuminate\Support\Str;

class MakeModelFormCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model-form';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model form';

    /**
     * Route prefix variable.
     *
     * @var string
     */
    protected $routePrefix;

    /**
     * Model name variable.
     *
     * @var string
     */
    protected $modelName;

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceOtherVariables($stub, ['DummyModel', 'DummyPrefix'], [$this->modelName, Str::plural(strtolower($this->routePrefix))])
            ->replaceClass($stub, $name);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/form.php';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $this->routePrefix = $this->argument('name');
        $this->modelName = ucfirst($this->routePrefix);
        return ucfirst(trim($this->argument('name'))) . ucfirst($this->type);
    }
}
