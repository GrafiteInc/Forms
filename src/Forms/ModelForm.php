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
     * The items loaded by the index
     *
     * @var mixed
     */
    public $items;

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

        if (! is_null($this->orientation)) {
            $this->builder->setOrientation($this->orientation);
        }

        if (! is_null($this->withJsValidation)) {
            $this->builder->setJsValidation($this->withJsValidation);
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

        if (!empty($this->confirmMessage) && is_null($this->confirmMethod)) {
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

    /**
     * And edit button for a model
     *
     * @return string
     */
    public function editButton($item)
    {
        $editLink = route($this->routes['edit'], [$item]);
        $buttonClasses = $this->buttonClasses['edit'] ?? 'btn btn-outline-primary btn-sm mr-2';

        $button = "<a class=\"{$buttonClasses}\" href=\"{$editLink}\">";
            $button .= $this->buttons['edit'] ?? 'Edit';
        $button .= "</a>";

        return $button;
    }

    /**
     * The headers with sort for the model index
     *
     * @return string
     */
    public function indexHeaders()
    {
        $headers = '';

        foreach ($this->parseVisibleFields($this->parseFields($this->fields())) as $header => $data) {
            $header = $data['label'] ?? $header;
            $header = ucfirst($header);
            $order = 'desc';

            if ($data['sortable']) {
                if (request('order') === 'desc') {
                    $order = 'asc';
                }

                if (request('order') === 'asc') {
                    $order = 'desc';
                }

                $sortLink = request()->url() . '?' . http_build_query(array_merge(
                    request()->all(),
                    [
                        'sort_by' => strtolower($header),
                        'order' => $order,
                    ]
                ));
                $icon = config('form-maker.html.sortable-icon', '&#8597;');

                $header = "<a href=\"{$sortLink}\">{$header} {$icon}</a>";
            }

            $headers .= "<th>{$header}</th>";
        }

        $headers .= config('form-maker.html.table-actions-header', '<th class="text-right">Actions</th>');

        return $headers;
    }

    /**
     * The index body for the model
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return string
     */
    public function indexBody($query = null)
    {
        $fields = $this->parseVisibleFields($this->parseFields($this->fields()));
        $sortBy = array_keys($fields)[0];
        $query = $query;

        if (is_null($query)) {
            $query = app($this->model);
        }

        if (!is_null($this->paginate)) {
            $this->items = $query
                ->with($this->with)
                ->orderBy(request('sort_by', $sortBy), request('order', 'asc'))
                ->paginate($this->paginate);
        } else {
            $this->items = $query
                ->with($this->with)
                ->orderBy(request('sort_by', $sortBy), request('order', 'asc'))
                ->get();
        }

        $rows = '';

        foreach ($this->items as $item) {
            $deleteButton = $this->delete($item);
            $editButton = $this->editButton($item);

            $rows .= "<tr>";
            foreach ($fields as $field => $data) {
                $rows .= "<td>{$item->$field}</td>";
            }

                $rows .= "<td>";
                    $rows .= " <div class=\"btn-toolbar justify-content-end\">";
                        $rows .= $editButton;
                        $rows .= $deleteButton;
                    $rows .= "</div>";
                $rows .= "</td>";
            $rows .= "</tr>";
        }

        return $rows;
    }

    /**
     *  A basic search form for the Form
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
            'class' => config('form-maker.form.search-class', 'form-inline')
        ]);

        $form .= '<div class="' . config('form-maker.form.before_after_input_wrapper', 'input-group') . '">';
            $form .= $this->field->makeInput('text', 'search', request('search'), [
                'placeholder' => $placeholder,
                'class' => config('form-maker.form.input-class', 'form-control')
            ]);
            $form .= '<div class="' . config('form-maker.form.input-group-after', 'input-group-append') . '">';
                $form .= $this->field->button($submitValue, [
                    'type' => 'submit',
                    'class' => config('form-maker.buttons.submit', 'btn btn-primary')
                ]);
            $form .= '</div>';
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
     * The index method for the model
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function index($query = null)
    {
        $indexHeaders = $this->indexHeaders();
        $indexBody = $this->indexBody($query);
        $paginated = '';

        if (!is_null($this->paginate)) {
            $paginated = $this->paginated();
        }

        $spacing = config('form-maker.html.pagination', 'd-flex justify-content-center mt-4 mb-0');
        $tableClass = config('form-maker.html.table', 'table table-borderless m-0 p-0');
        $tableHeadClass = config('form-maker.html.table-head', 'thead');

        $this->html = <<<EOT
<table class="{$tableClass}">
    <thead class="{$tableHeadClass}">
        <tr>
            $indexHeaders
        </tr>
    </thead>
    <tbody>
        $indexBody
    </tbody>
</table>

<div class="{$spacing}">{$paginated}</div>
EOT;

        return $this;
    }

    /**
     * Convert the items from the index to JSON
     *
     * @return void
     */
    public function toJson()
    {
        return $this->items->toJson();
    }

    /**
     * Check if a model instance is set
     *
     * @return boolean
     */
    public function hasInstance()
    {
        return !is_null($this->instance);
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

        foreach ($this->fields() as $settings) {
            $field = array_keys($settings)[0];
            if (!is_null($settings[$field]['factory'])) {
                $factory .= "\x20\x20\x20\x20\x20\x20\x20\x20'{$field}' => \$faker->{$settings[$field]['factory']},\n";
            }
        }

        return $factory;
    }
}
