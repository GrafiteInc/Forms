<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class AutosizeTextArea extends Field
{
    protected static function getType()
    {
        return 'textarea';
    }

    protected static function getAttributes()
    {
        return [
            'data-autosize',
            'rows' => 5,
        ];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/autosize@6.0.1/dist/autosize.min.js'
        ];
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_autosizeTextAreaField';
    }

    public static function js($id, $options)
    {
        return <<<JS
                _formsjs_autosizeTextAreaField = function (element) {
                    if (! element.getAttribute('data-formsjs-rendered')) {
                        autosize(element);

                        setTimeout(function() {
                            autosize.update(element);
                        }, 150);
                    }
                }
            JS;
    }
}
