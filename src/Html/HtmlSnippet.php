<?php

namespace Grafite\Forms\Html;

use Exception;
use Grafite\Forms\Services\HtmlConfigProcessor;
use Illuminate\Support\Str;

class HtmlSnippet
{
    public static $tag;

    public static function getHtmlOptions()
    {
        return [
            'content',
            'level',
        ];
    }

    public static function render($options = [])
    {
        return '';
    }

    public static function attributes($options = [])
    {
        foreach (self::getHtmlOptions() as $option) {
            unset($options[$option]);
        }

        $html = '';

        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $html .= " ".self::attributeElement($key, $value);
            }
        }

        return $html;
    }

    public static function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            return $value;
        }

        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="' . implode(' ', $value) . '"';
        }

        if (! is_null($value)) {
            return $key . '="' . e($value, false) . '"';
        }
    }

    public static function make($content = null, $name = null)
    {
        if (is_null($name)) {
            $name = 'html-snippet-' . Str::uuid();
        }

        $options = [
            'instance' => new static(),
            'type' => 'html',
            'content' => $content,
        ];

        if (is_array($content)) {
            $options = array_merge($options, $content);
        }

        return (new HtmlConfigProcessor($name, $options));
    }
}
