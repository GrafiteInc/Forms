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

    public static function getTemplate($options)
    {
        return <<<HTML
        <div class="{rowClass}">
            <label for="{id}" class="{labelClass}">{name}</label>
            <div class="{fieldClass}">
                <div id="Editor_{id}" class="editor_js_container"></div>
                {field}
                {errors}
            </div>
        </div>
HTML;
    }

    public static function styles($id, $options)
    {
        $themes = [];

        $themes['light'] = <<<CSS
    .editor_js_container {
        border-radius: 4px;
        border: 1px solid #ced4da;
        background-color: transparent;
        padding: 24px;
        width: 100%;
    }
CSS;

        $themes['dark'] = <<<CSS
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
CSS;

        if (isset($options['theme'])) {
            $theme = $themes[$options['theme']];
        }

        if (! isset($options['theme'])) {
            $lightTheme = $themes['light'];
            $darkTheme = $themes['dark'];

            $theme = <<<CSS
    @media (prefers-color-scheme: light) {
        {$lightTheme}
    }

    @media (prefers-color-scheme: dark) {
        {$darkTheme}
    }
CSS;
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

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_editorField';
    }

    public static function onLoadJsData($id, $options)
    {
        if (is_null($options['upload_route'])) {
            throw new \Exception('You need to set an `upload_route` for handling image uploads to EditorJs.', 1);
        }

        return json_encode([
            'route' => route($options['upload_route']),
            'placeholder' => $options['placeholder'] ?? 'Let`s write an awesome story!',
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
            _formsjs_editorField = function (element) {
                if (! element.getAttribute('data-formsjs-rendered')) {
                    let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                    let _Editor_value = element.value;

                    if (element.value == '') {
                        _Editor_value = "null";
                    }

                    let editor_js = new EditorJS({
                        holder: 'Editor_'+element.getAttribute('id'),
                        placeholder: _config.placeholder,
                        data: JSON.parse(_Editor_value),
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
                                        byFile: _config.route,
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

                    let _form = element.form;

                    _form.addEventListener('submit', function () {
                        editor_js.save().then((outputData) => {
                            element.value = JSON.stringify(outputData);
                        }).catch((error) => {
                            console.log('Saving failed: ', error)
                        });

                        return true;
                    });
                }
            }
JS;
    }
}
