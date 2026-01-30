<?php

namespace Grafite\Forms\Fields;

class Monaco extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getAttributes()
    {
        return [
            'style' => 'height: 300px;',
        ];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    public static function getTemplate($options)
    {
        $height = $options['height'] ?? '300px';

        return <<<HTML
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        {field}
        <div id="Monaco_{id}" style="height: {$height}; border: 1px solid #ced4da; border-radius: 4px;"></div>
        {errors}
    </div>
</div>
HTML;
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/monaco-editor@0.52.2/min/vs/loader.js',
        ];
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_monacoField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'language' => $options['language'] ?? 'javascript',
            'theme' => $options['theme'] ?? 'vs',
            'minimap' => $options['minimap'] ?? false,
            'lineNumbers' => $options['line_numbers'] ?? true,
            'wordWrap' => $options['word_wrap'] ?? 'on',
            'automaticLayout' => $options['automatic_layout'] ?? true,
            'readOnly' => $options['read_only'] ?? false,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<'JS'
        window._formsjs_monacoField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                let _containerId = 'Monaco_' + element.getAttribute('id');
                let _container = document.getElementById(_containerId);

                require.config({ paths: { 'vs': '//cdn.jsdelivr.net/npm/monaco-editor@0.52.2/min/vs' }});

                require(['vs/editor/editor.main'], function() {
                    let _editor = monaco.editor.create(_container, {
                        value: element.value || '',
                        language: _config.language,
                        theme: _config.theme,
                        minimap: { enabled: _config.minimap },
                        lineNumbers: _config.lineNumbers ? 'on' : 'off',
                        wordWrap: _config.wordWrap,
                        automaticLayout: _config.automaticLayout,
                        readOnly: _config.readOnly,
                    });

                    _editor.onDidChangeModelContent(function() {
                        element.value = _editor.getValue();
                        element.dispatchEvent(new Event('change', { bubbles: true }));
                    });

                    element._monacoEditor = _editor;
                });
            }
        }
JS;
    }
}
