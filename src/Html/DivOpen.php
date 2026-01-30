<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Builders\AttributeBuilder;

class DivOpen extends HtmlSnippet
{
    public static function render($options = [])
    {
        $attributes = app(AttributeBuilder::class)->render($options['attributes']);

        return "<div {$attributes}>";
    }
}
