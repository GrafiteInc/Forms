<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

class Toggle extends Field
{
    protected static function getType()
    {
        return 'checkbox';
    }

    protected static function getOptions()
    {
        return [
            'label' => false,
        ];
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'boolean';
    }

    protected static function stylesheets($options)
    {
        return [
            "//cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css",
        ];
    }

    protected static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js'
        ];
    }

    protected static function js($id, $options)
    {
        $theme = $options['theme'] ?? 'light';
        $on = $options['on'] ?? 'On';
        $off = $options['off'] ?? 'Off';
        $size = $options['size'] ?? 'sm';
        $labelClass = $options['label_class'] ?? 'bootstrap-toggle-label';

        return <<<EOT
$('#$id').bootstrapToggle({
    offstyle: "$theme",
    on: "$on",
    off: "$off",
    size: "$size"
});
$('#$id').parent().siblings('label').addClass('$labelClass')
EOT;
    }
}
