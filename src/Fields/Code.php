<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

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

    protected static function stylesheets($options)
    {
        $theme = $options['theme'] ?? 'default';

        return [
            "//cdnjs.cloudflare.com/ajax/libs/codemirror/5.53.2/theme/$theme.min.css",
            "//cdnjs.cloudflare.com/ajax/libs/codemirror/5.53.2/codemirror.min.css",
        ];
    }

    protected static function scripts($options)
    {
        $mode = $options['mode'] ?? 'htmlmixed';

        return [
            '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.53.2/codemirror.min.js',
            '//cdnjs.cloudflare.com/ajax/libs/codemirror/5.53.2/mode/xml/xml.min.js',
            "//cdnjs.cloudflare.com/ajax/libs/codemirror/5.53.2/mode/css/css.min.js",
            "//cdnjs.cloudflare.com/ajax/libs/codemirror/5.53.2/mode/$mode/$mode.min.js",
        ];
    }

    protected static function js($id, $options)
    {
        $mode = $options['mode'] ?? 'htmlmixed';
        $theme = $options['theme'] ?? 'default';

        return <<<EOT
CodeMirror.fromTextArea(document.getElementById("$id"), {
    lineNumbers: true,
    mode: '$mode',
    theme: '$theme',
});
EOT;
    }
}
