<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Choices extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker',
            'multiple' => true,
            'data-size' => 8,
        ];
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function stylesheets($options)
    {
        return [
            "//cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css",
        ];
    }

    public static function scripts($options)
    {
        return [
            "//cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js",
        ];
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_choicesField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'removeItemButton' => $options['removeItemButton'] ?? false,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_choicesField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _id = element.getAttribute('id');
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                new Choices(element, _config);
            }
        }
JS;
    }
}
