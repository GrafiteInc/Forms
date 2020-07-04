<?php

namespace Grafite\FormMaker\Components;

use Illuminate\View\Component;

class FormMaker extends Component
{
    public $content;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return (string) $this->content;
    }
}
