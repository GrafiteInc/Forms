<?php

namespace Grafite\Forms\Html;

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

        $config = (new HtmlConfigProcessor($name, $options));

        return $config;
    }
}
