<?php

namespace Grafite\Forms\Components;

use Illuminate\View\Component;

class FormModel extends Component
{
    public $form;
    public $action;
    public $model;
    public $modal;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($form, $action, $model = null, $modal = false)
    {
        $this->form = $form;
        $this->action = $action;
        $this->model = $model;
        $this->modal = $modal;
    }

    /**
     * Get the view / forms that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $action = $this->action;

        $form = app($this->form);

        if ($this->model) {
            $form->$action($this->model);
        }

        if (is_null($this->model)) {
            $form->$action();
        }

        if ($this->modal) {
            return (string) $form->asModal();
        }

        return (string) $form;
    }
}
