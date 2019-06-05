<?php

namespace Grafite\FormMaker\Forms;

use Form;
use Exception;
use Grafite\FormMaker\Services\FormMaker;

class ModelForm
{
    public $model;

    public $connection;

    public $columns = 1;

    public $hasFiles = false;

    public $routePrefix;

    public $fields = [];

    public $routes = [
        'create' => '',
        'update' => '.update',
        'delete' => '.destroy',
    ];

    public $methods = [
        'create' => 'post',
        'update' => 'put',
        'delete' => 'delete',
    ];

    public $buttons = [
        'save' => 'Save',
        'cancel' => 'Cancel',
    ];

    public $buttonLinks = [
        'cancel' => null,
    ];

    public $buttonClasses = [
        'save' => 'btn btn-primary',
        'cancel' => 'btn btn-secondary',
        'delete' => 'btn btn-danger',
    ];

    protected $html;

    protected $builder;

    public function __construct()
    {
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

        $this->builder = new FormMaker();

        if (is_null($this->buttonLinks['cancel'])) {
            $this->buttonLinks['cancel'] = url()->current();
        }

        foreach ($this->routes as $key => $route) {
            $this->routes[$key] = "{$this->routePrefix}{$route}";
        }
    }

    public function create()
    {
        $this->html = Form::open([
            'route' => [
                $this->routes['create']
            ],
            'files' => $this->hasFiles,
        ]);

        $this->html .= $this->builder
            ->setConnection($this->connection)
            ->setColumns($this->columns)
            ->fromTable($this->modelClass->getTable(), $this->fields);

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }

    public function edit($model)
    {
       $this->html = Form::model($model, [
            'route' => [
                $this->routes['update'], $model->id
            ],
            'method' => $this->methods['update'],
            'files' => $this->hasFiles,
        ]);

        $this->html .= $this->builder
            ->setConnection($this->connection)
            ->setColumns($this->columns)
            ->fromObject($model, $this->fields);

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }

    public function delete($model)
    {
        $this->html = Form::model($model, [
            'route' => [
                $this->routes['delete'], $model->id
            ],
            'method' => $this->methods['delete'],
            'class' => 'form-inline'
        ]);

        $this->html .= Form::submit('Delete', [
            'class' => $this->buttonClasses['delete']
        ]);

        $this->html .= Form::close();

        return $this;
    }

    public function formButtonsAndClose()
    {
        $lastRowInForm = '<div class="row"><div class="col-md-12 d-flex justify-content-between">';

        $lastRowInForm .= '<a class="'.$this->buttonClasses['cancel']
            .'" href="'.url($this->buttonLinks['cancel']).'">'.$this->buttons['cancel'].'</a>';

        $lastRowInForm .= Form::submit($this->buttons['save'], [
            'class' => 'btn btn-primary'
        ]);

        $lastRowInForm .= '</div></div>';

        $lastRowInForm .= Form::close();

        return $lastRowInForm;

    }

    public function getErrors()
    {
        return $this->builder->getFormErrors();
    }

    public function toHtml()
    {
        return $this->html;
    }
}
