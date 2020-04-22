<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Quill extends Field
{
    protected static function getType()
    {
        return 'div';
    }

    protected static function getAttributes()
    {
        return [
            'style' => 'height: 200px;'
        ];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    protected static function stylesheets($options)
    {
        return [
            "//cdn.quilljs.com/1.3.6/quill.bubble.css",
            "//cdn.quilljs.com/1.3.6/quill.snow.css",
        ];
    }

    protected static function scripts($options)
    {
        return ['//cdn.quilljs.com/1.3.6/quill.js'];
    }

    protected static function js($id, $options)
    {
        $theme = $options['theme'] ?? 'snow';
        $placeholder = $options['placeholder'] ?? '';

        return <<<EOT
        new Quill('#$id', {
            theme: '$theme',
            placeholder: '$placeholder'
        });
        EOT;
    }
}
