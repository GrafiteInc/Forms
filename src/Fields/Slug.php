<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Slug extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_slug_field';
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_slug_field = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                element.addEventListener("keyup", event => {
                    event.preventDefault();
                    let str = element.value;
                    str = str.replace(/\W+(?!$)/g, '-').toLowerCase();
                    element.value = str;
                });
            }
        }
JS;
    }
}
