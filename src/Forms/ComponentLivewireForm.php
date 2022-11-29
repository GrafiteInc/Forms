<?php

namespace Grafite\Forms\Forms;

use Grafite\Forms\Forms\LivewireForm;
use Grafite\Forms\Forms\Concerns\AsComponent;

class ComponentLivewireForm extends LivewireForm
{
    use AsComponent;

    public function render()
    {
        $this->make($this->data);

        return $this->html;
    }
}
