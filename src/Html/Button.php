<?php

namespace Grafite\Forms\Html;

use Illuminate\Support\HtmlString;
use Grafite\Forms\Html\HtmlSnippet;
use Grafite\Forms\Builders\AttributeBuilder;

class Button extends HtmlSnippet
{
    public static function render($options = [])
    {
        $options['class'] = $options['class'] ?? config('forms.buttons.submit');

        $attributes = app(AttributeBuilder::class)->render($options['attributes']);

        $content = '';

        if (isset($options['content'])) {
            $content = $options['content'];
        }

        throw_if(empty($content), 'You cannot have an empty button');

        return new HtmlString("<button {$attributes}>{$content}</button>");
    }
}
