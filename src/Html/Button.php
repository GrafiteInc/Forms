<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class Button extends HtmlSnippet
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

        return "<button{$attributes}>{$content}</button>";
    }
}
