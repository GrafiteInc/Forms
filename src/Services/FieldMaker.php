<?php

namespace Grafite\Forms\Services;

use Illuminate\Support\Str;
use Grafite\Forms\Traits\HasErrorBag;
use Grafite\Forms\Traits\HasLivewire;
use Grafite\Forms\Builders\FieldBuilder;

class FieldMaker
{
    use HasLivewire;
    use HasErrorBag;

    protected $builder;

    public $orientation;

    public $errorBag;

    public $withLivewire;

    public $livewireOnKeydown;

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
        'custom-file',
        'textarea',
        'relationship',
    ];

    protected $specialSelect = [
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
            ->setLivewireOnKeydown($this->livewireOnKeydown);

        if ($columnConfig['type'] === 'html') {
            return $columnConfig['content'];
        }

        $field = null;
        $fieldGroup = '';

        if (! isset($columnConfig['wrapper']) || $columnConfig['wrapper']) {
            $fieldGroup = config('forms.form.group-class', 'form-group');

            if ($this->orientation === 'horizontal') {
                $fieldGroup = $fieldGroup . ' ' . config('forms.form.sections.row-class', 'row');
            }
        }

        $value = $this->getOldValue($column);

        if (! is_null($object)) {
            $objectValue = $this->getObjectValue($object, $column);

            if (isset($objectValue)) {
                $value = $objectValue;
            }
        }

        $errors = $this->getFieldErrors($column, $object);

        $columnConfig = $this->setClassIfErrors($columnConfig, $errors);

        $label = $this->label(
            $column,
            $columnConfig,
            $columnConfig['label_class'] ?? null,
            $errors
        );

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
            $rowClass = config('forms.form.group-class', 'form-group');
            $labelClass = config('forms.form.label_class', 'control-label');
            $fieldClass = '';

            if ($this->orientation === 'horizontal') {
                $labelClass = config('forms.form.label-column', 'col-md-2 col-form-label pt-0');
                $fieldClass = config('forms.form.input-column', 'col-md-10');
                $rowClass = $rowClass . ' ' . config('forms.form.sections.row-class', 'row');
            }

            $options = $this->parseOptions($column, $columnConfig);
            $id = $options['attributes']['id'];

            $name = Str::title($column);
            $name = str_replace('_', ' ', $name);
            $name = $options['label'] ?? $name;

            return $this->fieldTemplate($columnConfig['template'], compact(
                'rowClass',
                'labelClass',
                'fieldClass',
                'label',
                'field',
                'errors',
                'id',
                'name'
            ));
        }

        if (isset($columnConfig['view'])) {
            $options = $this->parseOptions($column, $columnConfig);

            return view($columnConfig['view'], compact(
                'label',
                'field',
                'errors',
                'options'
            ))->render();
        }

        if (in_array($columnConfig['type'], $this->specialSelect)) {
            $label = '';

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
            $class = config('forms.form.label_class', 'control-label');
        }

        if (! empty($errors)) {
            $class = $class . ' ' . config('forms.form.error-class', 'has-error');
        }

        $id = $columnConfig['attributes']['id'] ?? $this->stripArrayHandles($column);

        if (empty($label)) {
            return '';
        }

        return "<label class=\"{$class}\" for=\"{$id}\">{$label}</label>";
    }

    public function wrapField($fieldGroup, $label, $fieldString, $errors)
    {
        if (Str::contains($fieldString, 'hidden')) {
            return $fieldString;
        }

        return "<div class=\"{$fieldGroup}\">{$label}{$fieldString}{$errors}</div>";
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
        $label = Str::title($column);
        $label = str_replace('_', ' ', $label);

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
            $class = config('forms.form.before_after_input_wrapper', 'input-group');
            $prefix = '<div class="' . $class . '">' . $columnConfig['before'];
        }

        return $prefix;
    }

    public function after($columnConfig)
    {
        $postfix = '';

        if (isset($columnConfig['before']) || isset($columnConfig['after'])) {
            $postfix = $columnConfig['after'] . '</div>';
        }

        return $postfix;
    }

    private function fieldTemplate($template, $options)
    {
        $keys = [];
        $values = [];
        $processedTemplate = '';

        foreach ($options as $key => $option) {
            $keys[] = "{{$key}}";
            $values[] = $option;
        }

        return str_replace($keys, $values, $template);
    }

    private function getOldValue($column)
    {
        if (session()->isStarted()) {
            return request()->old($column);
        }

        return null;
    }

    private function parseOptions($name, $options)
    {
        $default = [
            'class' => config('forms.form.input-class', 'form-control'),
            'id' => ucfirst($name),
        ];

        $options['attributes'] = array_merge($default, $options['attributes'] ?? []);

        return $options;
    }

    private function stripArrayHandles($column)
    {
        return str_replace('[]', '', ucfirst($column));
    }

    private function getNestedFieldLabel($label)
    {
        preg_match_all("/\[([^\]]*)\]/", $label, $matches);

        return $matches[1];
    }

    private function setClassIfErrors($columnConfig, $errors)
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
}
