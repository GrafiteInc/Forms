<?php

namespace Grafite\Forms\Forms;

use Illuminate\Routing\UrlGenerator;
use Grafite\Forms\Services\FormMaker;
use Grafite\Forms\Builders\FieldBuilder;

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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->url = app(UrlGenerator::class);
        $this->field = app(FieldBuilder::class);
        $this->session = session();

        $this->builder = app(FormMaker::class);

        if (is_null($this->buttonLinks['cancel'])) {
            $this->buttonLinks['cancel'] = request()->fullUrl();
        }

        if (! is_null($this->orientation)) {
            $this->builder->setOrientation($this->orientation);
        }

        if (! is_null($this->withJsValidation)) {
            $this->builder->setJsValidation($this->withJsValidation);
        }
    }

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
        if ($this->orientation == 'horizontal') {
            if ($this->formClass === config('forms.form.horizontal-class')) {
                $this->formClass = config('forms.form.horizontal-class', 'form-horizontal');
            }
        }

        $this->builder->setSections($this->setSections());

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

        $this->html .= $this->renderedFields;

        $this->html .= $this->formButtonsAndClose();

        $this->html = $this->asModal();

        return $this;
    }
}
