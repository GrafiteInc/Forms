<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class Heading extends HtmlSnippet
{
    public static function render($options = [])
    {
        $content = '';
        $level = 3;

        $attributes = self::attributes($options['attributes']);

        if (isset($options['content'])) {
            $content = $options['content'];
        }

        if (isset($options['level'])) {
            $level = $options['level'];
        }

        throw_if(empty($content), 'You cannot have an empty heading');

        return "<h{$level}{$attributes}>{$content}</h{$level}>";
    }
}
