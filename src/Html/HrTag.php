<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class HrTag extends HtmlSnippet
{
    public static function render($options = [])
    {
        $attributes = self::attributes($options['attributes']);

        return "<hr{$attributes}>";
    }
}
