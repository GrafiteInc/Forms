<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class DivOpen extends HtmlSnippet
{
    public static function content($options = [])
    {
        $class = '';

        if (isset($options['class'])) {
            $class = " class=\"{$options['class']}\"";
        }

        return "<div{$class}>";
    }
}
