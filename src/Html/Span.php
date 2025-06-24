<?php

namespace Grafite\Forms\Html;

use Illuminate\Support\HtmlString;
use Grafite\Forms\Html\HtmlSnippet;
use Grafite\Forms\Builders\AttributeBuilder;

class Span extends HtmlSnippet
{
    public static function render($options = [])
    {
        $content = '';

        $attributes = app(AttributeBuilder::class)->render($options['attributes']);

        if (isset($options['content'])) {
            $content = $options['content'];
        }

        throw_if(empty($content), 'You cannot have an empty span');

        return new HtmlString("<span {$attributes}>{$content}</span>");
    }
}
