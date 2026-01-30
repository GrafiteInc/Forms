<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Builders\AttributeBuilder;

class Heading extends HtmlSnippet
{
    public static function render($options = [])
    {
        $content = '';
        $level = 3;

        $attributes = app(AttributeBuilder::class)->render($options['attributes']);

        if (isset($options['content'])) {
            $content = $options['content'];
        }

        if (isset($options['level'])) {
            $level = $options['level'];
        }

        throw_if(empty($content), 'You cannot have an empty heading');

        return "<h{$level} {$attributes}>{$content}</h{$level}>";
    }
}
