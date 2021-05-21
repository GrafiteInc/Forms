<?php

namespace Grafite\Forms\Components;

use Illuminate\View\Component;

class FormBase extends Component
{
    public $form;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($form)
    {
        $this->form = $form;
    }

    /**
     * Get the view / forms that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return (string) app($this->form)->make();
    }
}
