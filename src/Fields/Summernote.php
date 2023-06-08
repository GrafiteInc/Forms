<?php

namespace Grafite\Forms\Fields;

class Summernote extends Field
{
    protected static function fieldOptions()
    {
        return [];
    }

    protected static function getType()
    {
        return 'textarea';
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js',
        ];
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_summernoteField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([]);
    }

    public static function js($id, $options)
    {
        return <<<JS
            _formsjs_summernoteField = function (element) {
                if (! element.getAttribute('data-formsjs-rendered')) {
                    let _id = element.getAttribute('id');
                    $(document).ready(function() {
                        $(element).summernote({
                            height: 400
                        });
                    });
                }
            };
JS;
    }
}
