<?php

namespace Grafite\Forms\Forms;

use Grafite\Forms\Forms\ModalForm;
use Grafite\Forms\Forms\Concerns\AsComponent;

class ComponentModalForm extends ModalForm
{
    use AsComponent;

    public function render()
    {
        $this->make();

        return $this->html;
    }
}
