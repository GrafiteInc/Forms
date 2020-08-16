<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Tags extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    protected static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.css'
        ];
    }

    protected static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.js'
        ];
    }

    protected static function styles($id, $options)
    {
        $defaultBorder = $options['default-border'] ?? '#EEE';
        $focusBorder = $options['focus-border'] ?? '#EEE';

        return <<<EOT
.tagify {
    --tags-border-color: $defaultBorder;
    --tags-focus-border-color: $focusBorder;
}
EOT;
    }

    protected static function js($id, $options)
    {
        return <<<EOT
new Tagify (document.getElementById('$id'));
EOT;
    }
}
