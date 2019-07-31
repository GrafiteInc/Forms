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
     * Form button classes
     *
     * @var array
     */
    public $buttonClasses = [
        'submit' => 'btn btn-primary',
        'cancel' => 'btn btn-secondary',
        'delete' => 'btn btn-danger',
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
        $this->url = app(UrlGenerator::class);
        $this->session = session();
        $this->field = app(FieldBuilder::class);
        $this->fields = $this->fields();

        if (empty($this->fields)) {
            throw new Exception("Invalid fields", 1);
        }

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

        $this->builder->setSections($this->setSections());

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
        $this->html = $this->open([
            'route' => [
                $this->routes['create']
            ],
            'files' => $this->hasFiles,
        ]);

        $fields = $this->parseFields($this->fields());

        $this->html .= $this->builder
            ->setConnection($this->connection)
            ->setColumns($this->columns)
            ->fromTable($this->modelClass->getTable(), $fields);

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
        $formClass = 'form';

        if ($this->orientation == 'horizontal') {
            $formClass = 'form-horizontal';
        }

        $this->html = $this->model($model, [
            'route' => [
                $this->routes['update'], $model->id
            ],
            'method' => $this->methods['update'],
            'files' => $this->hasFiles,
            'class' => $formClass,
        ]);

        $fields = $this->parseFields($this->fields());

        $this->html .= $this->builder
            ->setConnection($this->connection)
            ->setColumns($this->columns)
            ->fromObject($model, $fields);

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
        $this->html = $this->model($model, [
            'route' => [
                $this->routes['delete'], $model->id
            ],
            'method' => $this->methods['delete'],
            'class' => 'form-inline'
        ]);

        $options = [
            'class' => $this->buttonClasses['delete'],
        ];

        if (!empty($this->confirmMessage)) {
            $options = array_merge($options, [
                'onclick' => "return confirm('{$this->confirmMessage}')"
            ]);
        }

        $this->html .= $this->field->submit('Delete', $options);

        $this->html .= $this->close();

        return $this;
    }
}
