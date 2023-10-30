<?php

namespace Grafite\Forms\Services;

use Illuminate\Support\Str;
use Grafite\Forms\Traits\HasErrorBag;
use Grafite\Forms\Traits\HasLivewire;
use Grafite\Forms\Builders\FieldBuilder;
use Grafite\Forms\Builders\AttributeBuilder;

class FieldMaker
{
    use HasLivewire;
    use HasErrorBag;

    protected $builder;

    public $orientation;

    public $errorBag;

    public $withLivewire;

    public $livewireOnKeydown;

    public $livewireOnChange;

    protected $standard = [
        'hidden',
        'text',
        'number',
        'color',
        'email',
        'date',
        'datetime-local',
        'month',
        'range',
        'search',
        'tel',
        'time',
        'url',
        'week',
        'password',
        'time',
        'image',
        'file',
    ];

    protected $special = [
        'select',
        'datalist',
        'custom-file',
        'textarea',
        'relationship',
    ];

    protected $specialSelect = [
        'switch',
        'checkbox',
        'radio',
        'checkbox-inline',
        'radio-inline',
    ];

    public function __construct(FieldBuilder $fieldBuilder)
    {
        $this->builder = $fieldBuilder;
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

        if (in_array($columnConfig['type'], $this->specialSelect)) {
            $label = '';
        }

        if (isset($columnConfig['template']) || isset($columnConfig['view'])) {
            return $field;
        }

        $before = $this->before($columnConfig);
        $after = $this->after($columnConfig);

        $fieldString = $before . $field . $after;

        if ($this->orientation === 'horizontal') {
            $labelColumn = config('forms.form.label-column', 'col-md-2 col-form-label pt-0');
            $inputColumn = config('forms.form.input-column', 'col-md-10');

            $label = $this->label($column, $columnConfig, $labelColumn, $errors);

            if (in_array($columnConfig['type'], $this->specialSelect)) {
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
            $class = config('forms.form.label-class', 'control-label');
        }

        if (! empty($errors)) {
            $class .= ' ' . config('forms.form.error-class', 'has-error');
        }

        $id = $columnConfig['attributes']['id'] ?? $this->stripArrayHandles($column);

        if (empty($label)) {
            return '';
        }

        return "<label class=\"{$class}\" for=\"{$id}\">{$label}</label>";
    }

    public function wrapField($fieldGroup, $label, $fieldString, $errors)
    {
        if (Str::contains($fieldString, 'type="hidden"')) {
            return $fieldString;
        }

        if (! $fieldGroup) {
            return "{$label}{$fieldString}{$errors}";
        }

        $fieldAndLabel = $label . $fieldString;

        if (Str::of($fieldGroup)->contains('form-floating')) {
            $fieldAndLabel = $fieldString . $label;
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
        $label = Str::of($column)->title()->replace('_', ' ');

        if (Str::contains($label, '[')) {
            $label = $this->getNestedFieldLabel($label)[0];
        }

        if (isset($columnConfig['label'])) {
            $label = $columnConfig['label'];
        }

        return $label;
    }

    public function getFieldErrors($column)
    {
        $class = config('forms.form.invalid-feedback', 'invalid-feedback');

        $errors = collect([]);

        if (session()->isStarted()) {
            $errors = session('errors');
        }

        if (! is_null($this->errorBag)) {
            $errors = $this->errorBag;
            $column = 'data.' . $column;
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
            $class = config('forms.form.before-after-input-wrapper', 'input-group');
            $prefix = '<div class="' . $class . '">' . $columnConfig['before'];
        }

        return $prefix;
    }

    public function after($columnConfig)
    {
        $suffix = '';

        if (isset($columnConfig['before']) || isset($columnConfig['after'])) {
            $suffix = $columnConfig['after'] . '</div>';
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

        return str_replace($keys, $values, $template);
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
            'class' => config('forms.form.input-class', 'form-control'),
            'id' => ucfirst($name),
        ];

        if ($options['type'] === 'range') {
            $default['class'] = config('forms.form.range-class', 'form-range');
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
                . ' '
                . config('forms.form.input-class', 'form-control')
                . ' '
                . config('forms.form.invalid-input-class', 'is-invalid');
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

            $fieldGroupClass = is_string($columnConfig['wrapper']) ? $columnConfig['wrapper'] : config('forms.form.group-class', 'form-group');
        }

        if (! isset($columnConfig['wrapper'])) {
            $fieldGroupClass = config('forms.form.group-class', 'form-group');
        }

        if ($this->orientation === 'horizontal') {
            $fieldGroupClass .= ' ' . config('forms.form.sections.row-class', 'row');
        }

        return $fieldGroupClass;
    }

    protected function makeField($columnConfig, $label, $column, $value, $errors)
    {
        $field = null;

        if (in_array($columnConfig['type'], $this->standard)) {
            $field = $this->builder->makeInput(
                $columnConfig['type'],
                $column,
                $value,
                $this->parseOptions($column, $columnConfig)['attributes']
            );
        }

        if (in_array($columnConfig['type'], $this->special)) {
            $method = 'make' . ucfirst(Str::camel($columnConfig['type']));
            $field = $this->builder->$method(
                $column,
                $value,
                $this->parseOptions($column, $columnConfig)
            );
        }

        if (isset($columnConfig['template'])) {
            $options = $this->parseOptions($column, $columnConfig);
            $rowClass = config('forms.form.group-class', 'form-group');
            $labelClass = config('forms.form.label-class', 'control-label');
            $fieldClass = '';

            if ($this->orientation === 'horizontal') {
                $rowClass = config('forms.form.group-class', 'form-group') . ' ' . config('forms.form.sections.row-class', 'row');
                $labelClass = config('forms.form.label-column', 'col-md-2 col-form-label pt-0');
                $fieldClass = config('forms.form.input-column', 'col-md-10');
            }

            $name = $options['label'] ?? Str::of($column)->title()->replace('_', ' ');

            return $this->fieldTemplate($columnConfig['template'], [
                'rowClass' => $rowClass,
                'labelClass' => $labelClass,
                'fieldClass' => $fieldClass,
                'label' => $label,
                'field' => $field,
                'value' => $value,
                'errors' => $errors,
                'attributes' => app(AttributeBuilder::class)->render($options['attributes'], $name),
                'id' => $options['attributes']['id'],
                'name' => $name
            ]);
        }

        if (isset($columnConfig['view'])) {
            $options = $this->parseOptions($column, $columnConfig);

            return view($columnConfig['view'], [
                'label' => $label,
                'field' => $field,
                'errors' => $errors,
                'options' => $options,
            ])->render();
        }

        if (in_array($columnConfig['type'], $this->specialSelect)) {
            $field = $this->builder->makeCheckInput(
                $column,
                $value,
                $this->parseOptions($column, $columnConfig)
            );
        }

        if (is_null($field)) {
            $field = $this->builder->makeField(
                $columnConfig['type'],
                $column,
                $value,
                $this->parseOptions($column, $columnConfig)['attributes']
            );
        }

        return $field;
    }
}
