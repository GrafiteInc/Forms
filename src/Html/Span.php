<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class Span extends HtmlSnippet
{
    public static function render($options = [])
    {
        $content = '';

        $attributes = self::attributes($options['attributes']);

        if (isset($options['content'])) {
            $content = $options['content'];
        }

        throw_if(empty($content), 'You cannot have an empty span');

        return "<span{$attributes}>{$content}</span>";
    }
}
