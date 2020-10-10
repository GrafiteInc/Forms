<?php

namespace Grafite\Forms\Forms;

use Illuminate\Routing\UrlGenerator;
use Grafite\Forms\Forms\HtmlForm;
use Grafite\Forms\Services\FormMaker;
use Grafite\Forms\Builders\FieldBuilder;

class LivewireForm extends HtmlForm
{
    public $method = 'submit';

    public $onKeydown = false;

    public $withLivewire = true;

    public $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->url = app(UrlGenerator::class);
        $this->field = app(FieldBuilder::class);
        $this->session = session();

        $this->builder = app(FormMaker::class);

        if (! is_null($this->orientation)) {
            $this->builder->setOrientation($this->orientation);
        }
    }

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

        if ($this->orientation == 'horizontal') {
            if ($this->formClass === config('forms.form.horizontal-class')) {
                $this->formClass = config('forms.form.horizontal-class', 'form-horizontal');
            }
        }

        $this->builder->setSections($this->setSections());

        $options = [
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId,
            'wire:submit.prevent' => $this->method
        ];

        $this->html = $this->open($options);

        $fields = $this->parseFields($this->fields());

        $this->renderedFields = $this->builder
            ->setColumns($this->columns)
            ->setLivewire($this->withLivewire)
            ->setLivewireOnKeydown($this->onKeydown)
            ->setErrorBag($this->errorBag)
            ->setFormJs($this->scripts())
            ->fromFieldsOrObject($fields, $this->data);

        $this->html .= $this->renderedFields;

        $this->html .= $this->close();

        return $this;
    }
}
