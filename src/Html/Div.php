<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class Div extends HtmlSnippet
{
    public static function content($options = [])
    {
        $content = '';

        $attributes = self::attributes($options);

        if (isset($options['content'])) {
            $content = $options['content'];
        }

        throw_if(empty($content), 'You cannot have an empty div');

        return "<div{$attributes}>{$content}</div>";
    }
}
