<?php

namespace Grafite\Forms\Forms;

class BaseForm extends HtmlForm
{
    /**
     * The form route
     *
     * @var string
     */
    public $route;

    /**
     * The form method
     *
     * @var string
     */
    public $method = 'post';

    /**
     * Set the route
     *
     * @param string $name
     * @param mixed $parameters
     *
     * @return \Grafite\Forms\Forms\BaseForm
     */
    public function setRoute($name, $parameters = [])
    {
        if (is_array($parameters)) {
            $this->route = array_merge([ $name ], $parameters);
        } else {
            $this->route = [
                $name,
                $parameters
            ];
        }

        return $this;
    }

    /**
     * Create a form
     *
     * @return \Grafite\Forms\Forms\BaseForm
     */
    public function make()
    {
        if ($this->orientation === 'horizontal') {
            $this->formClass = $this->formClass ?? config('forms.form.horizontal-class', 'form-horizontal');
        }

        $this->builder->setSections($this->setSections());

        $options = [
            'route' => $this->route,
            'method' => $this->method,
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId
        ];

        if ($this->withLivewire) {
            $options['wire:submit.prevent'] = 'submit';
        }

        if ($this->submitOnKeydown) {
            $options['onkeydown'] = "{$this->submitMethod}(event)";
        }

        $this->html = $this->open($options);

        $fields = $this->parseFields($this->fields());

        $this->renderedFields = $this->builder
            ->setColumns($this->columns)
            ->setMaxColumns($this->maxColumns)
            ->setLivewire($this->withLivewire)
            ->setLivewireOnKeydown($this->livewireOnKeydown)
            ->setErrorBag($this->errorBag)
            ->setFormJs($this->scripts())
            ->setFormStyles($this->styles())
            ->fromFields($fields);

        if ($this->isCardForm) {
            $cardBody = config('forms.form.cards.card-body', 'card-body');
            $this->html .= "<div class=\"{$cardBody}\">";
        }

        $this->html .= $this->renderedFields;

        if ($this->isCardForm) {
            $this->html .= "</div>";
        }

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }
}
