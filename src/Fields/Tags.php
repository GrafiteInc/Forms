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

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/@yaireo/tagify@3.8.0/dist/tagify.min.js',
        ];
    }

    public static function styles($id, $options)
    {
        $defaultBorder = $options['default-border'] ?? '#EEE';
        $focusBorder = $options['focus-border'] ?? '#EEE';

        return <<<EOT
.tagify {
    --tags-border-color: {$defaultBorder};
    --tags-focus-border-color: {$focusBorder};
}
EOT;
    }

    public static function onLoadJs($id, $options)
    {
        return "_formjs_tagify";
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode($options['list'] ?? []);
    }

    public static function js($id, $options)
    {
        return <<<JS
        window._formsjs_tagify = function (element) {
            new Tagify (element, {
                whitelist: JSON.parse(element.getAttribute('data-formsjs-data'))
            });
        }
JS;
    }
}
