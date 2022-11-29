<?php

namespace Grafite\Forms\Forms;

use Grafite\Forms\Forms\WizardForm;
use Grafite\Forms\Forms\Concerns\AsComponent;

class ComponentWizardForm extends WizardForm
{
    use AsComponent;

    public function render()
    {
        $this->make();

        return $this->html;
    }
}
