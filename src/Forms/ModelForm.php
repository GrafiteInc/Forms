<?php

namespace Grafite\FormMaker\Forms;

use Exception;
use Illuminate\Routing\UrlGenerator;
use Grafite\FormMaker\Forms\HtmlForm;
use Grafite\FormMaker\Services\FormMaker;
use Grafite\FormMaker\Builders\FieldBuilder;

class ModelForm extends HtmlForm
{
    /**
     * Model class
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Model instance
     *
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    public $instance = null;

    /**
     * The database connection
     *
     * @var string
     */
    public $connection;

    /**
     * The route prefix, generally single form of model
     *
     * @var string
     */
    public $routePrefix;

    /**
     * Form routes
     *
     * @var array
     */
    public $routes = [
        'create' => '.store',
        'update' => '.update',
        'delete' => '.destroy',
    ];

    /**
     * Form methods
     *
     * @var array
     */
    public $methods = [
        'create' => 'post',
        'update' => 'put',
        'delete' => 'delete',
    ];

    /**
     * The field builder
     *
     * @var \Grafite\FormMaker\Builders\FieldBuilder
     */
    protected $builder;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->url = app(UrlGenerator::class);
        $this->session = session();
        $this->field = app(FieldBuilder::class);

        if (is_null($this->routePrefix)) {
            throw new Exception("Route Prefix is required, for example: users", 1);
        }

        if (is_null($this->connection)) {
            $this->connection = config('database.default');
        }

        $this->modelClass = app($this->model);
        $this->builder = app(FormMaker::class);

        if (is_null($this->buttonLinks['cancel'])) {
            $this->buttonLinks['cancel'] = url()->current();
        }

        if (!is_null($this->orientation)) {
            $this->builder->setOrientation($this->orientation);
        }

        foreach ($this->routes as $key => $route) {
            $this->routes[$key] = "{$this->routePrefix}{$route}";
        }
    }

    /**
     * A create form for a model
     *
     * @return \Grafite\FormMaker\Forms\ModelForm
     */
    public function create()
    {
        $this->builder->setSections($this->setSections());

        if ($this->orientation == 'horizontal') {
            if ($this->formClass === config('form-maker.form.horizontal-class')) {
                $this->formClass = config('form-maker.form.horizontal-class', 'form-horizontal');
            }
        }

        $this->html = $this->open([
            'route' => [
                $this->routes['create']
            ],
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId
        ]);

        $fields = $this->parseFields($this->fields());

        $this->renderedFields = $this->builder
            ->setConnection($this->connection)
            ->setColumns($this->columns)
            ->fromTable($this->modelClass->getTable(), $fields);

        $this->html .= $this->renderedFields;

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }

    /**
     * The edit form for a model
     *
     * @return \Grafite\FormMaker\Forms\ModelForm
     */
    public function edit($model)
    {
        $this->setInstance($model);

        $this->builder->setSections($this->setSections());

        if ($this->orientation == 'horizontal') {
            if ($this->formClass === config('form-maker.form.horizontal-class')) {
                $this->formClass = config('form-maker.form.horizontal-class', 'form-horizontal');
            }
        }

        $this->html = $this->model($this->instance, [
            'route' => [
                $this->routes['update'], $this->instance->id
            ],
            'method' => $this->methods['update'],
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId
        ]);

        $fields = $this->parseFields($this->fields());

        $this->renderedFields = $this->builder
            ->setConnection($this->connection)
            ->setColumns($this->columns)
            ->fromObject($this->instance, $fields);

        $this->html .= $this->renderedFields;

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }

    /**
     * A delete form for a model
     *
     * @return \Grafite\FormMaker\Forms\ModelForm
     */
    public function delete($model)
    {
        $this->setInstance($model);

        $this->builder->setSections($this->setSections());

        $this->html = $this->model($this->instance, [
            'route' => [
                $this->routes['delete'], $this->instance->id
            ],
            'method' => $this->methods['delete'],
            'class' => $this->formDeleteClass,
            'id' => $this->formId
        ]);

        $options = [
            'class' => $this->buttonClasses['delete'],
        ];

        if (!empty($this->confirmMessage) && is_null($this->confirmMessage)) {
            $options = array_merge($options, [
                'onclick' => "return confirm('{$this->confirmMessage}')"
            ]);
        }

        if (!empty($this->confirmMessage) && !is_null($this->confirmMethod)) {
            $options = array_merge($options, [
                'onclick' => "{$this->confirmMethod}(event, '{$this->confirmMessage}')"
            ]);
        }

        $options['type'] = 'submit';

        if ($this->formIsDisabled) {
            $options['disabled'] = 'disabled';
        }

        $this->html .= $this->field->button($this->buttons['delete'], $options);

        $this->html .= $this->close();

        return $this;
    }

    public function hasInstance()
    {
        return !is_null($this->instance);
    }

    public function setInstance($model)
    {
        $this->instance = $model;

        return $this;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function factoryFields()
    {
        $factory = '';

        foreach ($this->fields() as $settings) {
            $field = array_keys($settings)[0];
            if (!is_null($settings[$field]['factory'])) {
                $factory .= "\x20\x20\x20\x20\x20\x20\x20\x20'{$field}' => \$faker->{$settings[$field]['factory']},\n";
            }
        }

        return $factory;
    }
}
