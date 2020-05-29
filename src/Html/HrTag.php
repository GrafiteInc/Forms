<?php

namespace Grafite\FormMaker\Html;

use Grafite\FormMaker\Html\HtmlSnippet;

class HrTag extends HtmlSnippet
{
    public static function content($options = [])
    {
        $class = '';

        if (isset($options['class'])) {
            $class = " class=\"{$options['class']}\"";
        }

        return "<hr{$class}>";
    }
}
