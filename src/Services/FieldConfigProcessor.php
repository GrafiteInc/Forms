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
    public $instance;

    public function __construct($name, $options, $fieldInstance)
    {
        $this->name = $name;
        $this->fieldInstance = $fieldInstance;

        $this->processOptions($options);
    }

    protected function processOptions($options)
    {
        $this->type = $options['type'] ?? 'text';
        $this->options = array_merge($this->options, $options['options']);
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
        $this->processStaticMethods();

        return get_object_vars($this);
    }

    public function __toString()
    {
        $config = $this->toArray();

        return app(FieldMaker::class)->make($this->name, $config, $this->instance);
    }

    public function required($state = true)
    {
        $this->attributes['required'] = $state;

        return $this;
    }

    public function placeholder($value)
    {
        $this->attributes['placeholder'] = $value;

        return $this;
    }

    public function attribute($key, $value)
    {
        $this->attributes[$key] = $value;

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

    public function modelMethod($value)
    {
        $this->model_options['method'] = $value;

        return $this;
    }

    public function modelParams($value)
    {
        $this->model_options['params'] = $value;

        return $this;
    }

    public function modelValue($value)
    {
        $this->model_options['value'] = $value;

        return $this;
    }

    public function modelLabel($value)
    {
        $this->model_options['label'] = $value;

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

    public function sortable($state = true)
    {
        $this->sortable = $state;

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

    public function readonly($state = true)
    {
        $this->attributes = array_merge($this->attributes, [
            'readonly' => $state
        ]);

        return $this;
    }

    public function disabled($state = true)
    {
        $this->attributes = array_merge($this->attributes, [
            'disabled' => $state
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

    public function multiple($state = true)
    {
        $this->attributes = array_merge($this->attributes, [
            'multiple' => $state
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

    public function autofocus($state = true)
    {
        $this->attributes = array_merge($this->attributes, [
            'autofocus' => $state
        ]);

        return $this;
    }

    public function autocomplete($state = true)
    {
        $this->attributes = array_merge($this->attributes, [
            'autocomplete' => $state
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

    public function instance($value)
    {
        $this->instance = $value;

        return $this;
    }

    public function option($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function options($array)
    {
        $this->options = array_merge($this->options, $array);

        return $this;
    }

    public function unlabelled()
    {
        $this->label = false;

        return $this;
    }

    public function processStaticMethods()
    {
        if (is_null($this->template)) {
            $this->template = $this->fieldInstance::getTemplate($this->options);
        }

        $this->assets = array_merge($this->assets, [
            'js' => $this->fieldInstance::js(ucfirst($this->name), $this->options),
            'styles' => $this->fieldInstance::styles(ucfirst($this->name), $this->options) ?? null,
            'scripts' => $this->fieldInstance::scripts($this->options) ?? null,
            'stylesheets' => $this->fieldInstance::stylesheets($this->options) ?? null,
        ]);

        return $this;
    }
}
