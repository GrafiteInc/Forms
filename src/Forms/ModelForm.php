<?php

namespace Grafite\Forms\Forms;

use Exception;
use Illuminate\Support\Str;
use Grafite\Forms\Forms\Concerns\HasIndex;

class ModelForm extends HtmlForm
{
    use HasIndex;

    /**
     * Model class
     *
     * @var string
     */
    public $model;

    /**
     * Model class
     *
     * @var mixed
     */
    public $modelClass;

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
     * The route parameters often just an ID or MODEL but sometimes an array of values.
     *
     * @var array
     */
    public $routeParameters = ['id'];

    /**
     * The route parameter values
     *
     * @var array
     */
    public $routeParameterValues = [];

    /**
     * The number of items you want to paginate by
     *
     * @var null|int
     */
    public $paginate = null;

    /**
     * The relationships you want to load with the model
     *
     * @var array
     */
    public $with = [];

    /**
     * Should the delete form act as a modal?
     *
     * @var boolean
     */
    public $deleteAsModal = false;

    /**
     * Form submit on events
     *
     * @var array
     */
    public $submitOn = [
        'create' => null,
        'update' => null,
    ];

    /**
     * Form routes
     *
     * @var array
     */
    public $routes = [
        'create' => '.store',
        'edit' => '.edit',
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
     * Form submit methods
     *
     * @var array
     */
    public $submitMethods = [
        'create' => null,
        'update' => null,
        'delete' => null,
    ];

    /**
     * The form builder
     *
     * @var \Grafite\Forms\Services\FormMaker
     */
    protected $builder;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        if (is_null($this->routePrefix)) {
            throw new Exception('Route Prefix is required, for example: users', 1);
        }

        if (is_null($this->connection)) {
            $this->connection = config('database.default');
        }

        $this->modelClass = app($this->model);

        foreach ($this->routes as $key => $route) {
            $this->routes[$key] = "{$this->routePrefix}{$route}";
        }
    }

    /**
     * A create form for a model
     *
     * @return \Grafite\Forms\Forms\ModelForm
     */
    public function create()
    {
        $this->builder->setSections($this->setSections());
        $this->submitMethod = $this->submitMethods['create'] ?? null;

        if ($this->orientation === 'horizontal') {
            $this->formClass = $this->formClass ?? config('forms.form.horizontal-class', 'form-horizontal');
        }

        $options = [
            'route' => array_merge([$this->routes['create']], $this->routeParameterValues),
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId,
        ];

        if ($this->withLivewire) {
            $options['wire:submit.prevent'] = 'submit';
        }

        if (in_array('keydown', $this->submitOn['create'] ?? []) || $this->submitOnKeydown) {
            $options['data-formjs-onkeydown'] = "{$this->submitMethod}(event)";
        }

        if (in_array('change', $this->submitOn['create'] ?? []) || $this->submitOnChange) {
            $options['data-formsjs-onchange'] = "{$this->submitMethod}(event)";
        }

        $this->html = $this->open($options);

        $fields = $this->parseFields($this->fields());

        $this->renderedFields = $this->builder
            ->setConnection($this->connection)
            ->setColumns($this->columns)
            ->setMaxColumns($this->maxColumns)
            ->setLivewire($this->withLivewire)
            ->setLivewireOnKeydown($this->livewireOnKeydown)
            ->setLivewireOnChange($this->livewireOnChange)
            ->setErrorBag($this->errorBag)
            ->setFormJs($this->scripts())
            ->setFormStyles($this->styles())
            ->fromTable($this->modelClass->getTable(), $fields);

        if ($this->isCardForm) {
            $cardBody = config('forms.form.cards.card-body', 'card-body');
            $this->html .= "<div class=\"{$cardBody}\">";
        }

        $this->html .= $this->renderedFields;

        if ($this->isCardForm) {
            $this->html .= '</div>';
        }

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }

    /**
     * The edit form for a model
     *
     * @return \Grafite\Forms\Forms\ModelForm
     */
    public function edit($model = null)
    {
        if (! is_null($model)) {
            $this->setInstance($model);
        }

        $this->builder->setSections($this->setSections());
        $this->submitMethod = $this->submitMethods['update'] ?? null;

        $this->setRouteParameterValues();

        if ($this->orientation === 'horizontal') {
            $this->formClass = $this->formClass ?? config('forms.form.horizontal-class', 'form-horizontal');
        }

        $options = [
            'route' => array_merge([$this->routes['update']], $this->routeParameterValues),
            'method' => $this->methods['update'],
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId,
        ];

        if ($this->withLivewire) {
            $options['wire:submit.prevent'] = 'submit';
        }

        if (in_array('keydown', $this->submitOn['update'] ?? []) || $this->submitOnKeydown) {
            $options['data-formsjs-onkeydown'] = "{$this->submitMethod}(event)";
        }

        if (in_array('change', $this->submitOn['update'] ?? []) || $this->submitOnChange) {
            $options['data-formsjs-onchange'] = "{$this->submitMethod}(event)";
        }

        $this->html = $this->model($this->instance, $options);

        $fields = $this->parseFields($this->fields());

        $this->renderedFields = $this->builder
            ->setConnection($this->connection)
            ->setColumns($this->columns)
            ->setMaxColumns($this->maxColumns)
            ->setLivewire($this->withLivewire)
            ->setLivewireOnKeydown($this->livewireOnKeydown)
            ->setLivewireOnChange($this->livewireOnChange)
            ->setErrorBag($this->errorBag)
            ->setFormJs($this->scripts())
            ->setFormStyles($this->styles())
            ->fromObject($this->instance, $fields);

        if ($this->isCardForm) {
            $cardBody = config('forms.form.cards.card-body', 'card-body');
            $this->html .= "<div class=\"{$cardBody}\">";
        }

        $this->html .= $this->renderedFields;

        if ($this->isCardForm) {
            $this->html .= '</div>';
        }

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }

    /**
     * A delete form for a model
     *
     * @return \Grafite\Forms\Forms\ModelForm
     */
    public function delete($model = null)
    {
        if (! is_null($model)) {
            $this->setInstance($model);
        }

        $this->builder->setSections($this->setSections());
        $this->submitMethod = $this->submitMethods['delete'] ?? null;

        $this->setRouteParameterValues();
        $buttonAlignClass = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? 'float-end' : 'float-right';
        $formDeleteClass = ($this->deleteAsModal) ? $this->formDeleteClass . ' ' . $buttonAlignClass : $this->formDeleteClass;
        $id = $this->instance->id;
        $instanceClass = Str::of(get_class($this->instance))->explode('\\')->last();

        $this->html = $this->model($this->instance, [
            'route' => array_merge([$this->routes['delete']], $this->routeParameterValues),
            'method' => $this->methods['delete'],
            'class' => $formDeleteClass,
            'id' => $this->formId,
            'wire:submit.prevent' => ($this->withLivewire) ? "delete($id, '$instanceClass')" : null
        ]);

        $options = [
            'class' => $this->buttonClasses['delete'],
        ];

        $options['data-formsjs-confirm-message'] = (! empty($this->confirmMessage)) ? $this->confirmMessage : false;
        $options['data-formsjs-onclick'] = $this->getConfirmationOption($options);
        $options['type'] = 'submit';

        if ($this->formIsDisabled) {
            $options['disabled'] = 'disabled';
        }

        $deleteButton = $this->buttons['delete'];
        $confirmMessage = $this->confirmMessage ?? 'Are you sure you want to delete this?';

        if ($this->deleteAsModal) {
            $this->message = "<p class=\"mb-4\">{$confirmMessage}</p>";
            $this->triggerClass = $this->buttonClasses['delete'];
            $this->triggerContent = $this->buttons['delete'];
            $deleteButton = $this->buttons['confirm'];
            $options['class'] = $this->buttonClasses['confirm'];
        }

        $this->html .= $this->field->button($deleteButton, $options);

        $this->html .= $this->close();

        if ($this->deleteAsModal) {
            $this->modalTitle = 'Confirmation';
            $this->formId = "{$this->formId}_Delete";
            $this->html = $this->asModal();
        }

        return $this;
    }

    /**
     * And edit button for a model
     *
     * @return string
     */
    public function editButton($item = null)
    {
        if (! is_null($item)) {
            $this->setInstance($item);
        }

        $this->setRouteParameterValues();

        $editLink = route($this->routes['edit'], $this->routeParameterValues);

        $buttonClasses = $this->buttonClasses['edit'] ?? 'btn btn-outline-primary btn-sm mr-2';

        $button = "<a class=\"{$buttonClasses}\" href=\"{$editLink}\">";
        $button .= $this->buttons['edit'] ?? 'Edit';
        $button .= '</a>';

        return $button;
    }

    /**
     *  A basic search form for the Model
     *
     * @param string $route
     * @param string $placeholder
     * @return string
     */
    public function search($route, $placeholder = 'Search', $submitValue = 'Search', $method = 'post')
    {
        $form = $this->open([
            'route' => $route,
            'method' => $method,
            'class' => config('forms.form.search-class', 'form-inline'),
        ]);

        $form .= '<div class="' . config('forms.form.before-after-input-wrapper', 'input-group') . '">';
        $form .= $this->field->makeInput('text', 'search', request('search'), [
            'placeholder' => $placeholder,
            'class' => config('forms.form.input-class', 'form-control'),
        ]);

        if (! Str::of(config('forms.bootstrap-version'))->startsWith('5')) {
            $form .= '<div class="' . config('forms.form.input-group-after', 'input-group-append') . '">';
        }

        $form .= $this->field->button($submitValue, [
            'type' => 'submit',
            'class' => config('forms.buttons.submit', 'btn btn-primary'),
        ]);

        if (! Str::of(config('forms.bootstrap-version'))->startsWith('5')) {
            $form .= '</div>';
        }

        $form .= '</div>';

        $form .= $this->close();

        return $form;
    }

    /**
     * The items paginated string of the items
     *
     * @return string
     */
    public function paginated()
    {
        return $this->items->__toString();
    }

    /**
     * Parse the fields for visible ones
     *
     * @param array $fields
     * @return array
     */
    public function parseVisibleFields($fields)
    {
        return collect($fields)->filter(function ($field) {
            return $field['visible'];
        })->toArray();
    }

    /**
     * Check if a model instance is set
     *
     * @return bool
     */
    public function hasInstance()
    {
        return ! is_null($this->instance);
    }

    /**
     * Set the model instance for a Form
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return self
     */
    public function setInstance($model)
    {
        $this->instance = $model;

        return $this;
    }

    /**
     * Get the model instance for the form
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Generate the factory for the fields
     *
     * @return string
     */
    public function factoryFields()
    {
        $factory = '';

        foreach ($this->fields() as $fieldConfig) {
            $settings = $fieldConfig->toArray();
            $field = $settings['name'];

            if (! is_null($settings['factory'])) {
                $factory .= "\x20\x20\x20\x20\x20\x20\x20\x20'{$field}' => \$faker->{$settings['factory']},\n";
            }
        }

        return $factory;
    }

    public function setRouteParameterValues($values = [])
    {
        $this->routeParameterValues = $values;

        if (empty($values)) {
            foreach ($this->routeParameters as $key) {
                $this->routeParameterValues[] = $this->instance->$key;
            }
        }

        return $this;
    }

    protected function getConfirmationOption($options)
    {
        $onclick = false;

        if (! empty($this->confirmMessage) && is_null($this->confirmMethod)) {
            $onclick = "FormsJS_confirm(event)";
        }

        if (! empty($this->confirmMessage) && is_null($this->confirmMethod) && $this->submitViaAjax) {
            $onclick = "FormsJS_confirmForAjax(event)";
        }

        if (! empty($this->confirmMessage) && ! is_null($this->confirmMethod)) {
            $onclick = "{$this->confirmMethod}(event)";
        }

        if (is_null($this->confirmMethod) && $this->deleteAsModal) {
            return 'FormsJS_disableOnSubmit(event)';
        }

        return $onclick;
    }
}
