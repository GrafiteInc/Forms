<?php

namespace Grafite\Forms\Components;

use Illuminate\View\Component;

class FormAction extends Component
{
    public $route;
    public $method;
    public $content;
    public $options;
    public $confirm;
    public $confirmMessage;
    public $confirmMethod;
    public $payload;
    public $disableOnSubmit;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $route,
        $content = "",
        $payload = [],
        $method = "post",
        $options = [],
        $confirm = false,
        $confirmMessage = "Are you sure you want to complete this action?",
        $confirmMethod = "confirm",
        $disableOnSubmit = false
    ) {
        $this->route = $route;
        $this->method = $method;
        $this->content = $content;
        $this->payload = $payload;
        $this->options = $options;
        $this->confirm = $confirm;
        $this->confirmMessage = $confirmMessage;
        $this->confirmMethod = $confirmMethod;
        $this->disableOnSubmit = $disableOnSubmit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $form = form();

        if ($this->confirm) {
            $form->confirm($this->confirmMessage, $this->confirmMethod);
        }

        return (string) $form->payload($this->payload)
            ->action($this->method, $this->route, $this->content, $this->options, false, $this->disableOnSubmit);
    }
}
