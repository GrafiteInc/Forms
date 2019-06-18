<?php

namespace Grafite\FormMaker\Services;

use Exception;
use Illuminate\Support\Str;
use Grafite\FormMaker\Builders\FieldBuilder;

class FieldMaker
{
    protected $builder;

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
        $field = null;
        $withErrors = false;
        $fieldGroup = config('form-maker.form.group-class', 'form-group');

        $value = $this->getOldValue($column);

        if (!is_null($object)) {
            $value = $object->$column;
        }

        $errors = $this->getFieldErrors($column, $object);

        if (!empty($errors)) {
            $withErrors = true;
        }

        $label = $this->label($column, $columnConfig, $withErrors);

        if (in_array($columnConfig['type'], $this->standard)) {
            $field = $this->builder->makeInput(
                $columnConfig['type'],
                $column,
                $value,
                $this->parseOptions($column, $columnConfig)['attributes']
            );
        }

        if (in_array($columnConfig['type'], $this->special)) {
            $method = 'make'.ucfirst(Str::camel($columnConfig['type']));
            $field = $this->builder->$method(
                $column,
                $value,
                $this->parseOptions($column, $columnConfig)
            );
        }

        if (isset($columnConfig['view'])) {
            $options = $this->parseOptions($column, $columnConfig);

            return view($columnConfig['view'], compact(
                'label',
                'field',
                'errors',
                'options'))->render();
        }

        if (in_array($columnConfig['type'], $this->specialSelect)) {
            return $this->builder->makeCheckInput(
                $column,
                $value,
                $this->parseOptions($column, $columnConfig)
            );
        }

        if (is_null($field)) {
            throw new Exception("Unknown field type.", 1);
        }

        return $this->wrapField($fieldGroup, $label, $field, $errors, $columnConfig);
    }

    public function label($column, $columnConfig, $withErrors = false)
    {
        $label = ucfirst($column);
        $class = config('form-maker.form.label-class', 'control-label');

        if ($columnConfig['label']) {
            $label = $columnConfig['label'];
        }

        if ($withErrors) {
            $class = $class.' '.config('form-maker.form.error-class', 'has-error');
        }

        return "<label class=\"{$class}\" for=\"{$this->stripArrayHandles($column)}\">{$label}</label>";
    }

    public function wrapField($fieldGroup, $label, $field, $errors, $columnConfig)
    {
        $before = $this->before($columnConfig);
        $after = $this->after($columnConfig);

        return "<div class=\"{$fieldGroup}\">{$label}{$before}{$field}{$after}</div>{$errors}";
    }

    public function getFieldErrors($column)
    {
        $errors = [];

        if (session()->isStarted()) {
            $errors = session('errors');
        }

        if (!is_null($errors) && count($errors) > 0) {
            $message = implode(' ', $errors->get($column));
            return "<div><p class=\"text-danger\">{$message}</p></div>";
        }

        return '';
    }

    public function before($columnConfig)
    {
        $prefix = '';

        if (isset($columnConfig['before']) || isset($columnConfig['after'])) {
            $class = config('form-maker.form.before_after_input_wrapper', 'input-group');
            $prefix = '<div class="'.$class.'">'.$columnConfig['before'];
        }

        return $prefix;
    }

    public function after($columnConfig)
    {
        $postfix = '';

        if (isset($columnConfig['before']) || isset($columnConfig['after'])) {
            $postfix = $columnConfig['after'].'</div>';
        }

        return $postfix;
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
            'class' => 'form-control',
            'id' => ucwords(str_replace('_', ' ', $name)),
        ];


        $options['attributes'] = array_merge($default, $options['attributes']);

        return $options;
    }

    private function stripArrayHandles($column)
    {
        return str_replace('[]', '', ucfirst($column));
    }
}
