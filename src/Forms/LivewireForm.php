<?php

namespace Grafite\Forms\Forms;

class LivewireForm extends HtmlForm
{
    public $method = 'submit';

    public $onKeydown = false;

    public $onChange = false;

    public $withLivewire = true;

    public $data;

    /**
     * Create a form
     *
     * @return \Grafite\Forms\Forms\BaseForm
     */
    public function make($data = [])
    {
        $this->data = $data;

        if ($this->onKeydown) {
            $this->livewireOnKeydown = true;
        }

        if ($this->onChange) {
            $this->livewireOnChange = true;
        }

        if ($this->orientation === 'horizontal') {
            $this->formClass = $this->formClass ?? config('forms.form.horizontal-class', 'form-horizontal');
        }

        $options = [
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId,
            'wire:submit.prevent' => $this->method,
        ];

        $this->html = $this->open($options);
        $this->setUp();
        $fields = $this->parseFields($this->fields());
        $this->builder->setSections($this->setSections($fields));

        $this->renderedFields = $this->builder
            ->setColumns($this->columns)
            ->setMaxColumns($this->maxColumns)
            ->setLivewire($this->withLivewire)
            ->setLivewireOnKeydown($this->onKeydown)
            ->setErrorBag($this->errorBag)
            ->setFormJs($this->scripts())
            ->setFormStyles($this->styles())
            ->fromFieldsOrObject($fields, $this->data);

        $this->html .= $this->renderedFields;

        $this->html .= $this->close();

        return $this;
    }
}
