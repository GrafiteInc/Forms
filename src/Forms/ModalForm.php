<?php

namespace Grafite\Forms\Forms;

class ModalForm extends HtmlForm
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
     * Trigger content (usually a button)
     *
     * @var string|null
     */
    public $triggerContent = 'Trigger Modal';

    /**
     * Trigger class (usually for a button)
     *
     * @var string|null
     */
    public $triggerClass = null;

    /**
     * Set the route
     *
     * @param string $name
     * @param array $parameters
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
                $parameters,
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

        $options = [
            'route' => $this->route,
            'method' => $this->method,
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId,
        ];

        if ($this->withLivewire) {
            $options['wire:submit.prevent'] = 'submit';
        }

        $this->html = $this->open($options);
        $this->setUp();
        $fields = $this->parseFields($this->fields());
        $this->builder->setSections($this->setSections($fields));

        $this->renderedFields = $this->builder
            ->setColumns($this->columns)
            ->setMaxColumns($this->maxColumns)
            ->setLivewire($this->withLivewire)
            ->setLivewireOnKeydown($this->livewireOnKeydown)
            ->setLivewireOnChange($this->livewireOnChange)
            ->setErrorBag($this->errorBag)
            ->setFormJs($this->scripts())
            ->setFormStyles($this->styles())
            ->fromFields($fields);

        $this->html .= $this->renderedFields;

        $this->html .= $this->formButtonsAndClose();

        $this->html = $this->asModal();

        return $this;
    }
}
