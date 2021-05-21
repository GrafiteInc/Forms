<?php

namespace Grafite\Forms\Components;

use Illuminate\View\Component;

class FormModel extends Component
{
    public $form;
    public $action;
    public $model;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($form, $action, $model)
    {
        $this->form = $form;
        $this->action = $action;
        $this->model = $model;
    }

    /**
     * Get the view / forms that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $action = $this->action;

        return (string) app($this->form)->$action($this->model);
    }
}
