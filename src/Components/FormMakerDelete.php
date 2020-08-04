<?php

namespace Grafite\FormMaker\Components;

use Illuminate\View\Component;

class FormMakerDelete extends Component
{
    public $confirm;

    public $confirmMessage;

    public $confirmMethod;

    public $form;

    public $item;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $form,
        $item,
        $confirm = false,
        $confirmMessage = "Are you sure you want to delete this item?",
        $confirmMethod = "confirm"
    ) {
        $this->item = $item;
        $this->form = $form;
        $this->confirm = $confirm;
        $this->confirmMessage = $confirmMessage;
        $this->confirmMethod = $confirmMethod;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $form = app($this->form);

        if ($this->confirm) {
            $form->confirm($this->confirmMessage, $this->confirmMethod);
        }

        return (string) $form->delete($this->item);
    }
}
