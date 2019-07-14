<?php

namespace Grafite\FormMaker\Forms;

use Exception;
use Grafite\FormMaker\Forms\Form;
use Grafite\FormMaker\Builders\FieldBuilder;
use Grafite\FormMaker\Services\FormMaker;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\UrlGenerator;

class ModelForm extends Form
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
     * The form orientation
     *
     * @var string
     */
    public $orientation;

    /**
     * Number of columns for the form
     *
     * @var integer
     */
    public $columns = 1;

    /**
     * Whether or not the form has files
     *
     * @var boolean
     */
    public $hasFiles = false;

    /**
     * The route prefix, generally single form of model
     *
     * @var string
     */
    public $routePrefix;

    /**
     * Form fields as array
     *
     * @var array
     */
    public $fields = [];

    /**
     * Form routes
     *
     * @var array
     */
    public $routes = [
        'create' => '',
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
     * Form button words
     *
     * @var array
     */
    public $buttons = [
        'save' => 'Save',
        'cancel' => 'Cancel',
    ];

    /**
     * Form button links
     *
     * @var array
     */
    public $buttonLinks = [
        'cancel' => null,
    ];

    /**
     * Form button classes
     *
     * @var array
     */
    public $buttonClasses = [
        'save' => 'btn btn-primary',
        'cancel' => 'btn btn-secondary',
        'delete' => 'btn btn-danger',
    ];

    /**
     * Html string for output
     *
     * @var string
     */
    protected $html;

    /**
     * Message for delete confirmation
     *
     * @var string
     */
    public $confirmMessage;

    /**
     * The field builder
     *
     * @var \Grafite\FormMaker\Builders\FieldBuilder
     */
    protected $builder;

    /**
     * Constructor
     *
     * @param UrlGenerator $url
     * @param Factory $view
     * @param Request $request
     * @param FieldBuilder $fieldBuilder
     */
    public function __construct(
        UrlGenerator $url,
        Factory $view,
        FieldBuilder $fieldBuilder
    ) {
        $this->url = $url;
        $this->view = $view;
        $this->field = $fieldBuilder;
        $this->session = session();
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
                $this->routes['update'],
                $model->id
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
                $this->routes['delete'],
                $model->id
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

    /**
     * Append to the html the close form with buttons
     *
     * @return string
     */
    protected function formButtonsAndClose()
    {
        $flexAlignment = (isset($this->buttons['cancel'])) ? 'between' : 'end';

        $lastRowInForm = '<div class="row"><div class="col-md-12 d-flex justify-content-'.$flexAlignment.'">';

        if (isset($this->buttons['cancel'])) {
            $lastRowInForm .= '<a class="'.$this->buttonClasses['cancel']
                .'" href="'.url($this->buttonLinks['cancel']).'">'.$this->buttons['cancel'].'</a>';
        }

        $lastRowInForm .= $this->field->submit($this->buttons['save'], [
            'class' => 'btn btn-primary'
        ]);

        $lastRowInForm .= '</div></div>'.$this->close();

        return $lastRowInForm;
    }

    /**
     * Set the confirmation message for delete forms
     *
     * @param string $message
     *
     * @return \Grafite\FormMaker\Forms\ModelForm
     */
    public function confirm($message)
    {
        $this->confirmMessage = $message;

        return $this;
    }

    /**
     * Set the fields
     *
     * @return \Grafite\FormMaker\Forms\ModelForm
     */
    public function fields()
    {
        return [];
    }

    /**
     * Parse the fields to get proper config
     *
     * @param array $formFields
     *
     * @return array
     */
    protected function parseFields($formFields)
    {
        $fields = [];

        foreach ($formFields as $config) {
            $key = array_key_first($config);
            $fields[$key] = $config[$key];
        }

        return $fields;
    }

    /**
     * Output html as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->html;
    }
}
