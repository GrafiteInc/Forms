<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Code extends Field
{
    protected static function getType()
    {
        return 'textarea';
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

    public static function stylesheets($options)
    {
        $theme = $options['theme'] ?? 'default';

        return [
            "//cdn.jsdelivr.net/npm/codemirror@5.65.16/theme/$theme.min.css",
            '//cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.min.css',
        ];
    }

    public static function scripts($options)
    {
        $mode = $options['mode'] ?? 'htmlmixed';

        return [
            '//cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.min.js',
            '//cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/xml/xml.min.js',
            "//cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/css/css.min.js",
            "//cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/$mode/$mode.min.js",
        ];
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_codeField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'mode' => $options['mode'] ?? 'htmlmixed',
            'theme' => $options['theme'] ?? 'default',
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_codeField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                let cm = CodeMirror.fromTextArea(element, {
                    lineNumbers: true,
                    mode: _config.mode,
                    theme: _config.theme,
                });

                window.addEventListener('keyup', function (event) {
                    element.value = cm.getDoc().getValue();
                });
            }
        }
JS;
    }
}
