<?php

namespace Grafite\Forms\Html;

use Exception;
use Grafite\Forms\Services\HtmlConfigProcessor;
use Illuminate\Support\Str;

class HtmlSnippet
{
    public static function getHtmlOptions()
    {
        return [
            'content',
            'level',
        ];
    }

    public static function content($options = [])
    {
        return [];
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
        if (is_array($content) || is_null($content)) {
            $content = static::content($content);
        }

        throw_if(is_null($content), new Exception('Content cannot be null'));

        if (is_null($name)) {
            $name = 'html-snippet-' . Str::uuid();
        }

        $options = [
            'type' => 'html',
            'content' => $content,
        ];

        return (new HtmlConfigProcessor($name, $options));
    }
}
