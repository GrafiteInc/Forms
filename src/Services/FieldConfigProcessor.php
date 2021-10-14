<?php

namespace Grafite\Forms\Services;

use Grafite\Forms\Services\FieldMaker;

class FieldConfigProcessor
{
    public $name;
    public $options = [];
    public $type;
    public $legend;
    public $label;
    public $model;
    public $null_value;
    public $null_label;
    public $model_options;
    public $before;
    public $after;
    public $view;
    public $template;
    public $attributes;
    public $format;
    public $visible;
    public $sortable;
    public $wrapper;
    public $table_class;
    public $label_class;

    public function __construct($name, $options)
    {
        $this->name = $name;

        $this->processOptions($options);
    }

    protected function processOptions($options)
    {
        $this->type = $options['type'] ?? 'text';
        $this->options = $options['options'] ?? [];
        $this->visible = $options['visible'] ?? true;
        $this->attributes = $options['attributes'] ?? [];
        $this->assets = $options['assets'] ?? [];
        $this->factory = $options['factory'] ?? null;
        $this->template = $options['template'] ?? null;
        $this->view = $options['view'] ?? null;
        $this->before = $options['before'] ?? null;
        $this->after = $options['after'] ?? null;
        $this->wrapper = $options['wrapper'] ?? true;
        $this->sortable = $options['sortable'] ?? false;
        $this->format = $options['format'] ?? null;
        $this->legend = $options['legend'] ?? null;
        $this->label = $options['label'] ?? null;
        $this->model = $options['model'] ?? null;
        $this->null_value = $options['null_value'] ?? false;
        $this->null_label = $options['null_label'] ?? 'None';
        $this->table_class = $options['table_class'] ?? null;
        $this->label_class = $options['label_class'] ?? null;
        $this->model_options = [
            'label' => $options['model_options']['label'] ?? 'name',
            'value' => $options['model_options']['value'] ?? 'id',
            'params' => $options['model_options']['params'] ?? null,
            'method' => $options['model_options']['method'] ?? 'all',
        ];

        if (isset($options['class'])) {
            $this->attributes([
                'class' => $options['class'],
            ]);
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function __toString()
    {
        $config = $this->toArray();

        return app(FieldMaker::class)->make($this->name, $config);
    }

    public function required()
    {
        $this->attributes['required'] = true;

        return $this;
    }

    public function placeholder($value)
    {
        $this->attributes['placeholder'] = $value;

        return $this;
    }

    public function attributes($value)
    {
        $this->attributes = array_merge($this->attributes, $value);

        return $this;
    }

    public function value($value)
    {
        $this->attributes['value'] = $value;

        return $this;
    }

    public function label($value)
    {
        $this->label = $value;

        return $this;
    }

    public function name($value)
    {
        $this->name = $value;

        return $this;
    }

    public function accept($fileTypes)
    {
        $this->attributes = array_merge($this->attributes, ['accept' => implode(',', $fileTypes)]);

        return $this;
    }

    public function before($value)
    {
        $this->before = $value;

        return $this;
    }

    public function after($value)
    {
        $this->after = $value;

        return $this;
    }

    public function legend($value)
    {
        $this->legend = $value;

        return $this;
    }

    public function model($value)
    {
        $this->model = $value;

        return $this;
    }

    public function groupClass($value)
    {
        $this->wrapper = $value;

        return $this;
    }

    public function ungrouped()
    {
        $this->wrapper = false;

        return $this;
    }

    public function withoutLabel()
    {
        $this->label = false;

        return $this;
    }

    public function onlyField()
    {
        $this->label = false;
        $this->wrapper = false;

        return $this;
    }

    public function sortable()
    {
        $this->sortable = true;

        return $this;
    }

    public function canSelectNone()
    {
        $this->null_value = true;

        return $this;
    }

    public function noneLabel($value)
    {
        $this->null_label = $value;

        return $this;
    }

    public function visible()
    {
        $this->visible = true;

        return $this;
    }

    public function hidden()
    {
        $this->visible = false;

        return $this;
    }

    public function tableClass($value)
    {
        $this->table_class = $value;

        return $this;
    }

    public function labelClass($value)
    {
        $this->label_class = $value;

        return $this;
    }

    public function cssClass($value)
    {
        $this->attributes = array_merge($this->attributes, ['class' => $value]);

        return $this;
    }

    public function view($value)
    {
        $this->view = $value;

        return $this;
    }

    public function template($value)
    {
        $this->template = $value;

        return $this;
    }

    public function readonly()
    {
        $this->attributes = array_merge($this->attributes, [
            'readonly' => true
        ]);

        return $this;
    }

    public function disabled()
    {
        $this->attributes = array_merge($this->attributes, [
            'disabled' => true
        ]);

        return $this;
    }

    public function maxlength($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'maxlength' => $value
        ]);

        return $this;
    }

    public function size($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'size' => $value
        ]);

        return $this;
    }

    public function min($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'min' => $value
        ]);

        return $this;
    }

    public function max($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'max' => $value
        ]);

        return $this;
    }

    public function pattern($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'pattern' => $value
        ]);

        return $this;
    }

    public function multiple()
    {
        $this->attributes = array_merge($this->attributes, [
            'multiple' => true
        ]);

        return $this;
    }

    public function step($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'step' => $value
        ]);

        return $this;
    }

    public function autofocus()
    {
        $this->attributes = array_merge($this->attributes, [
            'autofocus' => true
        ]);

        return $this;
    }

    public function autocomplete()
    {
        $this->attributes = array_merge($this->attributes, [
            'autocomplete' => true
        ]);

        return $this;
    }

    public function id($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'id' => $value
        ]);

        return $this;
    }

    public function style($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'style' => $value
        ]);

        return $this;
    }

    public function title($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'title' => $value
        ]);

        return $this;
    }

    public function data($key, $value)
    {
        $this->attributes = array_merge($this->attributes, [
            'data-'.$key => $value
        ]);

        return $this;
    }

    public function selectOptions($array)
    {
        $this->options = array_merge($this->options, $array);

        return $this;
    }
}
