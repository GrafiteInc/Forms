<?php

namespace Grafite\Forms\Components;

use Illuminate\View\Component;

class FormSearch extends Component
{
    public $index;

    public $route;

    public $content;

    public $placeholder;

    public $method;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $index,
        $route,
        $content = '',
        $placeholder = '',
        $method = 'post'
    ) {
        $this->index = $index;
        $this->route = $route;
        $this->method = $method;
        $this->content = $content;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return (string) $this->index->search(
            $this->route,
            $this->placeholder,
            $this->content,
            $this->method
        );
    }
}
