<?php

namespace Grafite\Forms\Services;

use Grafite\Forms\Builders\FieldBuilder;
use Grafite\Forms\Traits\HasErrorBag;
use Grafite\Forms\Traits\HasLivewire;
use Illuminate\Support\Str;

class FieldMaker
{
    use HasErrorBag;
    use HasLivewire;

    protected $builder;

    public $orientation;

    public $errorBag;

    public $withLivewire;

    public $livewireOnKeydown;

    public $livewireOnChange;

    /**
     * Per-instance memo of resolved config values. Config does not change
     * within a request, so each key is resolved from the container once
     * instead of on every field.
     *
     * @var array<string, mixed>
     */
    protected $configCache = [];

    protected $standard = [
        'hidden' => true,
        'text' => true,
        'number' => true,
        'color' => true,
        'email' => true,
        'date' => true,
        'datetime-local' => true,
        'month' => true,
        'range' => true,
        'search' => true,
        'tel' => true,
        'time' => true,
        'url' => true,
        'week' => true,
        'password' => true,
        'image' => true,
        'file' => true,
    ];

    protected $special = [
        'select' => true,
        'datalist' => true,
        'custom-file' => true,
        'textarea' => true,
        'relationship' => true,
    ];

    protected $specialSelect = [
        'switch' => true,
        'checkbox' => true,
        'radio' => true,
        'checkbox-inline' => true,
        'radio-inline' => true,
    ];

    public function __construct(FieldBuilder $fieldBuilder)
    {
        $this->builder = $fieldBuilder;
    }

    /**
     * Resolve a config value once per instance.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    protected function cfg($key, $default = null)
    {
        if (! array_key_exists($key, $this->configCache)) {
            $this->configCache[$key] = config($key, $default);
        }

        return $this->configCache[$key];
    }

    public function make(string $column, array $columnConfig, $object = null)
    {
        $this->builder
            ->setLivewire($this->withLivewire)
            ->setLivewireOnKeydown($this->livewireOnKeydown)
            ->setLivewireOnChange($this->livewireOnChange);

        if ($columnConfig['type'] === 'html') {
            return $columnConfig['instance']::render($columnConfig);
        }

        $field = null;
        $fieldGroup = $this->getFieldGroup($columnConfig);
        $value = $this->getOldValue($column);

        if (! is_null($object)) {
            $value = $this->getObjectValue($object, $column) ?? $value;
        }

        $errors = $this->getFieldErrors($column, $object);
        $columnConfig = $this->setClassIfErrors($columnConfig, $errors);

        $label = $this->label(
            $column,
            $columnConfig,
            $columnConfig['label_class'] ?? null,
            $errors
        );

        $field = $this->makeField($columnConfig, $label, $column, $value, $errors);

        if (isset($this->specialSelect[$columnConfig['type']])) {
            $label = '';
        }

        if (isset($columnConfig['template']) || isset($columnConfig['view'])) {
            return $field;
        }

        $before = $this->before($columnConfig);
        $after = $this->after($columnConfig);

        $fieldString = $before.$field.$after;

        if ($this->orientation === 'horizontal') {
            $labelColumn = $this->cfg('forms.form.label-column', 'col-md-2 col-form-label pt-0');
            $inputColumn = $this->cfg('forms.form.input-column', 'col-md-10');

            $label = $this->label($column, $columnConfig, $labelColumn, $errors);

            if (isset($this->specialSelect[$columnConfig['type']])) {
                $legend = $columnConfig['legend'] ?? $columnConfig['label'];
                $label = "<legend class=\"{$labelColumn}\">{$legend}</legend>";
            }

            $fieldString = "<div class=\"{$inputColumn}\">{$fieldString}{$errors}</div>";
            $errors = null;
        }

        return $this->wrapField($fieldGroup, $label, $fieldString, $errors);
    }

    public function label($column, $columnConfig, $class, $errors)
    {
        $label = $this->getLabel($column, $columnConfig);

        if (is_null($class)) {
            $class = $this->cfg('forms.form.label-class', 'control-label');
        }

        if (! empty($errors)) {
            $class .= ' '.$this->cfg('forms.form.error-class', 'has-error');
        }

        $id = $columnConfig['attributes']['id'] ?? $this->stripArrayHandles($column);

        if (empty($label)) {
            return '';
        }

        return "<label class=\"{$class}\" for=\"{$id}\">{$label}</label>";
    }

    public function wrapField($fieldGroup, $label, $fieldString, $errors)
    {
        if (str_contains($fieldString, 'type="hidden"')) {
            return $fieldString;
        }

        if (! $fieldGroup) {
            return "{$label}{$fieldString}{$errors}";
        }

        $fieldAndLabel = $label.$fieldString;

        if (str_contains($fieldGroup, 'form-floating')) {
            $fieldAndLabel = $fieldString.$label;
        }

        return "<div class=\"{$fieldGroup}\">{$fieldAndLabel}{$errors}</div>";
    }

    public function getObjectValue($object, $name)
    {
        if (is_object($object) && isset($object->$name)) {
            return $object->$name;
        }

        // If its a nested value like meta[user[phone]]
        if (strpos($name, '[') > 0) {
            $nested = explode('[', str_replace(']', '', $name));
            $final = $object;

            foreach ($nested as $property) {
                if (! empty($property) && isset($final->{$property})) {
                    $final = $final->{$property};
                } elseif (is_object($final) && is_null($final->{$property})) {
                    $final = '';
                }
            }

            return $final;
        }

        return '';
    }

    public function getLabel($column, $columnConfig)
    {
        $label = str_replace('_', ' ', Str::title($column));

        if (str_contains($label, '[')) {
            $label = $this->getNestedFieldLabel($label)[0];
        }

        if (isset($columnConfig['label'])) {
            $label = $columnConfig['label'];
        }

        return $label;
    }

    public function getFieldErrors($column)
    {
        $class = $this->cfg('forms.form.invalid-feedback', 'invalid-feedback');

        $errors = collect([]);

        if (session()->isStarted()) {
            $errors = session('errors');
        }

        if (! is_null($this->errorBag)) {
            $errors = $this->errorBag;
            $column = 'data.'.$column;
        }

        if (! is_object($errors)) {
            $errors = collect($errors);
        }

        if (! is_null($errors) && count($errors) > 0 && $errors->get($column)) {
            $message = implode(' ', $errors->get($column));
            $message = str_replace('data.', '', $message);

            return "<div class=\"{$class}\">{$message}</div>";
        }

        return '';
    }

    public function before($columnConfig)
    {
        $prefix = '';

        if (isset($columnConfig['before']) || isset($columnConfig['after'])) {
            $class = $this->cfg('forms.form.before-after-input-wrapper', 'input-group');
            $prefix = '<div class="'.$class.'">'.$columnConfig['before'];
        }

        return $prefix;
    }

    public function after($columnConfig)
    {
        $suffix = '';

        if (isset($columnConfig['before']) || isset($columnConfig['after'])) {
            $suffix = $columnConfig['after'].'</div>';
        }

        return $suffix;
    }

    protected function fieldTemplate($template, $options)
    {
        $keys = [];
        $values = [];

        foreach ($options as $key => $option) {
            $keys[] = "{{$key}}";
            $values[] = $option;
        }

        $fieldHtml = str_replace($keys, $values, $template);

        if (str_contains($fieldHtml, '></label>')) {
            $fieldHtmlAsArray = explode("\n", $fieldHtml);
            unset($fieldHtmlAsArray[1]);
            $fieldHtml = implode("\n", $fieldHtmlAsArray);
        }

        return $fieldHtml;
    }

    protected function getOldValue($column)
    {
        if (session()->isStarted()) {
            return request()->old($column);
        }

        return null;
    }

    protected function parseOptions($name, $options)
    {
        $default = [
            'class' => $this->cfg('forms.form.input-class', 'form-control'),
            'id' => ucfirst($name),
        ];

        if ($options['type'] === 'range') {
            $default['class'] = $this->cfg('forms.form.range-class', 'form-range');
        }

        if (in_array($options['type'], ['select', 'relationship'])) {
            $default['class'] = $this->cfg('forms.form.select-class', 'form-select');
        }

        $options['attributes'] = array_merge($default, $options['attributes'] ?? []);

        return $options;
    }

    protected function stripArrayHandles($column)
    {
        return str_replace('[]', '', ucfirst($column));
    }

    protected function getNestedFieldLabel($label)
    {
        preg_match_all("/\[([^\]]*)\]/", $label, $matches);

        return $matches[1];
    }

    protected function setClassIfErrors($columnConfig, $errors)
    {
        if (! empty($errors)) {
            $currentClass = $columnConfig['attributes']['class'] ?? ' ';

            $columnConfig['attributes']['class'] = $currentClass
                .' '
                .$this->cfg('forms.form.input-class', 'form-control')
                .' '
                .$this->cfg('forms.form.invalid-input-class', 'is-invalid');
        }

        return $columnConfig;
    }

    protected function getFieldGroup($columnConfig)
    {
        $fieldGroupClass = '';

        if (isset($columnConfig['wrapper'])) {
            if (! $columnConfig['wrapper']) {
                return false;
            }

            $fieldGroupClass = is_string($columnConfig['wrapper']) ? $columnConfig['wrapper'] : $this->cfg('forms.form.group-class', 'form-group');
        }

        if (! isset($columnConfig['wrapper'])) {
            $fieldGroupClass = $this->cfg('forms.form.group-class', 'form-group');
        }

        if ($this->orientation === 'horizontal') {
            $fieldGroupClass .= ' '.$this->cfg('forms.form.sections.row-class', 'row');
        }

        return $fieldGroupClass;
    }

    protected function makeField($columnConfig, $label, $column, $value, $errors)
    {
        $field = null;
        $options = $this->parseOptions($column, $columnConfig);

        if (isset($this->standard[$columnConfig['type']])) {
            $field = $this->builder->makeInput(
                $columnConfig['type'],
                $column,
                $value,
                $options['attributes']
            );
        }

        if (isset($this->special[$columnConfig['type']])) {
            $method = 'make'.ucfirst(Str::camel($columnConfig['type']));
            $field = $this->builder->$method(
                $column,
                $value,
                $options
            );
        }

        if (isset($columnConfig['template'])) {
            $rowClass = $this->cfg('forms.form.group-class', 'form-group');
            $labelClass = $this->cfg('forms.form.label-class', 'control-label');
            $fieldClass = '';

            if ($this->orientation === 'horizontal') {
                $rowClass = $this->cfg('forms.form.group-class', 'form-group').' '.$this->cfg('forms.form.sections.row-class', 'row');
                $labelClass = $this->cfg('forms.form.label-column', 'col-md-2 col-form-label pt-0');
                $fieldClass = $this->cfg('forms.form.input-column', 'col-md-10');
            }

            $name = $options['label'] ?? str_replace('_', ' ', Str::title($column));

            return $this->fieldTemplate($columnConfig['template'], [
                'rowClass' => $rowClass,
                'labelClass' => $labelClass,
                'fieldClass' => $fieldClass,
                'label' => $label,
                'field' => $field,
                'value' => $value,
                'errors' => $errors,
                'attributes' => $this->builder->attributeBuilder->render($options['attributes'], $name),
                'id' => $options['attributes']['id'],
                'name' => $name,
            ]);
        }

        if (isset($columnConfig['view'])) {
            return view($columnConfig['view'], [
                'label' => $label,
                'field' => $field,
                'errors' => $errors,
                'options' => $options,
            ])->render();
        }

        if (isset($this->specialSelect[$columnConfig['type']])) {
            $field = $this->builder->makeCheckInput(
                $column,
                $value,
                $options
            );
        }

        if (is_null($field)) {
            $field = $this->builder->makeField(
                $columnConfig['type'],
                $column,
                $value,
                $options['attributes']
            );
        }

        return $field;
    }
}
