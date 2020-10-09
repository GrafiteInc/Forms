<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class Span extends HtmlSnippet
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

        throw_if(empty($content), 'You cannot have an empty span');

        return "<span{$class}>{$content}</span>";
    }
}
