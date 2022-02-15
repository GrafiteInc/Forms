<?php

namespace Grafite\Forms\Fields;

class Grapes extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'string';
    }

    public static function getTemplate($options)
    {
        return <<<EOT
        <div class="{rowClass}">
            <label for="{id}" class="{labelClass}">{name}</label>
            <div class="{fieldClass}">
                <div id="Editor_{id}" class="editor_js_container"></div>
                {field}
                {errors}
            </div>
        </div>
EOT;
    }

    public static function styles($id, $options)
    {
        $themes = [];

        $themes['light'] = <<<EOT
    .editor_js_container {
        border-radius: 4px;
        border: 1px solid #ced4da;
        background-color: transparent;
        padding: 24px;
        width: 100%;
    }
EOT;

        $themes['dark'] = <<<EOT
    .editor_js_container {
        border-radius: 4px;
        border: 2px solid #333;
        background-color: transparent;
        padding: 24px;
        width: 100%;
    }

    .editor_js_container .ce-toolbox, .editor_js_container .ce-settings {
        background: #333;
    }

    .editor_js_container .codex-editor svg {
        fill: #FFF;
    }

    .editor_js_container .ce-toolbox__button:hover, .editor_js_container.tc-toolbox__toggler:hover {
        background-color: #222;
    }

    .editor_js_container .ce-toolbar__actions div:hover, .editor_js_container .ce-toolbar__actions span:hover {
        background-color: #222;
    }

    .editor_js_container .ce-code__textarea {
        background: transparent;
        color: #FFF;
    }
EOT;

        if (isset($options['theme'])) {
            $theme = $themes[$options['theme']];
        }

        if (! isset($options['theme'])) {
            $lightTheme = $themes['light'];
            $darkTheme = $themes['dark'];

            $theme = <<<EOT
    @media (prefers-color-scheme: light) {
        ${lightTheme}
    }

    @media (prefers-color-scheme: dark) {
        ${darkTheme}
    }
EOT;
        }

        return $theme;
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/@editorjs/editorjs@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/header@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/paragraph@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/link@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/underline@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/table@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/quote@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/checklist@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/marker@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/inline-code@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/code@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/list@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/image@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/embed@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/warning@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/raw@latest',
            '//cdn.jsdelivr.net/npm/@editorjs/delimiter@latest',
        ];
    }

    public static function js($id, $options)
    {
        $route = route($options['upload_route']);
        $placeholder = $options['placeholder'] ?? 'Let`s write an awesome story!';

        if (is_null($route)) {
            throw new \Exception('You need to set an `upload_route` for handling image uploads to EditorJs.', 1);
        }

        return <<<EOT
let _Editor_{$id}_value = document.getElementById('{$id}').value;

if (_Editor_{$id}_value == '') {
    _Editor_{$id}_value = "null";
}

const editor_{$id} = new EditorJS({
    holder: 'Editor_{$id}',
    placeholder: '{$placeholder}',
    data: JSON.parse(_Editor_{$id}_value),
    tools: {
        header: Header,
        delimiter: Delimiter,
        paragraph: {
            class: Paragraph,
            inlineToolbar: true,
        },
        list: {
            class: List,
            inlineToolbar: true,
        },
        embed: Embed,
        image: {
            class: ImageTool,
            config: {
                additionalRequestHeaders: {
                    "X-CSRF-TOKEN": document.head.querySelector('meta[name="csrf-token"]').content
                },
                endpoints: {
                    byFile: '{$route}',
                }
            }
        },
        underline: Underline,
        table: Table,
        quote: Quote,
        checklist: Checklist,
        marker: Marker,
        inlineCode: InlineCode,
        code: CodeTool,
        raw: RawTool,
        warning: Warning
    },
});

let _form = document.getElementById('{$id}').form;

_form.addEventListener('submit', function () {
    editor_{$id}.save().then((outputData) => {
        document.getElementById('{$id}').value = JSON.stringify(outputData);
    }).catch((error) => {
        console.log('Saving failed: ', error)
    });

    return true;
});

EOT;
    }
}
