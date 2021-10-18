<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;
use Grafite\Forms\Builders\AttributeBuilder;

class HrTag extends HtmlSnippet
{
    public static function render($options = [])
    {
        $attributes = app(AttributeBuilder::class)->render($options['attributes']);

        return "<hr {$attributes}>";
    }
}
