<?php

namespace Grafite\Forms\Forms;

use Grafite\Forms\Forms\BaseForm;
use Grafite\Forms\Forms\Concerns\AsComponent;

class ComponentForm extends BaseForm
{
    use AsComponent;

    public function render()
    {
        $this->make();

        return $this->html;
    }
}
