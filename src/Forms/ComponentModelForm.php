<?php

namespace Grafite\Forms\Forms;

use Grafite\Forms\Forms\ModelForm;
use Grafite\Forms\Forms\Concerns\AsComponent;

class ComponentModelForm extends ModelForm
{
    use AsComponent;

    public $instance;

    public function __construct($instance = null)
    {
        $this->instance = $instance;

        parent::__construct();
    }

    public function render()
    {
        $this->create();

        if ($this->instance) {
            $this->edit($this->instance);
        }

        return $this->html;
    }
}
