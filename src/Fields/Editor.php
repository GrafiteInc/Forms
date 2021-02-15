<?php

namespace Grafite\Forms\Fields;

class Editor extends Field
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

    protected static function getTemplate()
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

    protected static function styles($id, $options)
    {
        $theme = $options['theme'] ?? 'light';

        $borderColor = 'ced4da';
        $backgroundColor = 'FFF';
        $borderThickness = '1';

        if ($theme === 'dark') {
            $borderColor = '333';
            $backgroundColor = '1F1F1F';
            $borderThickness = '2';
        }

        return <<<EOT
.editor_js_container {
    border-radius: 4px;
    border: ${borderThickness}px solid #${borderColor};
    background-color: #${backgroundColor};
    padding: 24px;
    width: 100%;
}
EOT;
    }

    protected static function scripts($options)
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
            '//cdn.jsdelivr.net/npm/@editorjs/delimiter@latest',
        ];
    }

    protected static function js($id, $options)
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
