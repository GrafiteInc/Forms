<?php

namespace Grafite\FormMaker\Forms;

use Exception;
use Illuminate\Routing\UrlGenerator;
use Grafite\FormMaker\Forms\HtmlForm;
use Grafite\FormMaker\Services\FormMaker;
use Grafite\FormMaker\Builders\FieldBuilder;

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
     * Constructor
     */
    public function __construct()
    {
        $this->url = app(UrlGenerator::class);
        $this->field = app(FieldBuilder::class);
        $this->session = session();
        $this->fields = $this->fields();

        if (empty($this->fields)) {
            throw new Exception("Invalid fields", 1);
        }

        $this->builder = app(FormMaker::class);

        if (is_null($this->buttonLinks['cancel'])) {
            $this->buttonLinks['cancel'] = url()->current();
        }

        if (!is_null($this->orientation)) {
            $this->builder->setOrientation($this->orientation);
        }

        $this->builder->setSections($this->setSections());
    }

    /**
     * Set the route
     *
     * @param string $name
     * @param array $parameters
     *
     * @return \Grafite\FormMaker\Forms\BaseForm
     */
    public function setRoute($name, $parameters = [], $absolute = true)
    {
        $this->route = [
            $name,
            $parameters
        ];

        return $this;
    }

    /**
     * Create a form
     *
     * @return \Grafite\FormMaker\Forms\BaseForm
     */
    public function make()
    {
        $formClass = 'form';

        if ($this->orientation == 'horizontal') {
            $formClass = 'form-horizontal';
        }

        $this->html = $this->open([
            'route' => $this->route,
            'method' => $this->method,
            'files' => $this->hasFiles,
            'class' => $formClass,
        ]);

        $fields = $this->parseFields($this->fields());

        $this->html .= $this->builder
            ->setColumns($this->columns)
            ->fromFields($fields);

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }
}
