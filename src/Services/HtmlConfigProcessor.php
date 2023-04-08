<?php

namespace Grafite\Forms\Services;

use Grafite\Forms\Services\FieldMaker;

class HtmlConfigProcessor
{
    public $name;
    public $options = [];
    public $type;
    public $content;
    public $attributes;
    public $level;
    public $instance;

    public function __construct($name, $options)
    {
        $this->name = $name;

        $this->processOptions($options);
    }

    protected function processOptions($options)
    {
        $this->instance = $options['instance'] ?? null;
        $this->type = $options['type'] ?? 'html';
        $this->options = $options['options'] ?? [];
        $this->content = $options['content'] ?? null;
        $this->attributes = $options['attributes'] ?? [];
        $this->level = $options['level'] ?? null;

        if (isset($options['class'])) {
            $this->attributes([
                'class' => $options['class'],
            ]);
        }

        if (isset($options['href'])) {
            $this->attributes([
                'href' => $options['href'],
            ]);
        }

        if (isset($options['id'])) {
            $this->attributes([
                'id' => $options['id'],
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

    public function attributes($value)
    {
        $this->attributes = array_merge($this->attributes, $value);

        return $this;
    }

    public function disabled()
    {
        $this->attributes = array_merge($this->attributes, [
            'disabled' => true
        ]);

        return $this;
    }

    public function cssClass($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'class' => $value
        ]);

        return $this;
    }

    public function level($value)
    {
        $this->level = $value;

        return $this;
    }

    public function href($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'href' => $value
        ]);

        return $this;
    }

    public function name($value)
    {
        $this->name = $value;

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

    public function data($key, $value)
    {
        $this->attributes = array_merge($this->attributes, [
            'data-'.$key => $value
        ]);

        return $this;
    }

    public function hidden($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'hidden' => $value
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

    public function onClick($value)
    {
        $this->attributes = array_merge($this->attributes, [
            'onclick' => $value
        ]);

        return $this;
    }
}
