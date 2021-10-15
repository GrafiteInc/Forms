<?php

namespace Grafite\Forms\Html;

class Link extends HtmlSnippet
{
    public static function render($options = [])
    {
        $options['class'] = $options['class'] ?? config('forms.buttons.submit');

        $attributes = self::attributes($options['attributes']);

        $content = '';

        if (isset($options['content'])) {
            $content = $options['content'];
        }

        throw_if(empty($content), 'You cannot have an empty button');

        return "<a{$attributes}>{$content}</a>";
    }
}
