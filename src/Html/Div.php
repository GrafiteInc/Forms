<?php

namespace Grafite\FormMaker\Html;

use Grafite\FormMaker\Html\HtmlSnippet;

class Div extends HtmlSnippet
{
    public static function content($options = [])
    {
        $class = '';
        $content = '';

        if (isset($options['class'])) {
            $class = " class=\"{$options['class']}\"";
        }

        if (isset($options['content'])) {
            $content = $options['content'];
        }

        throw_if(empty($content), 'You cannot have an empty heading');

        return "<div{$class}>{$content}</div>";
    }
}
