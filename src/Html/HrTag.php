<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class HrTag extends HtmlSnippet
{
    public static function content($options = [])
    {
        $attributes = self::attributes($options);

        return "<hr{$attributes}>";
    }
}
