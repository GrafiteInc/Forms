<?php

namespace Grafite\Forms\Fields;

class Quill extends Field
{
    protected static function fieldOptions()
    {
        return [
            'quill_theme',
            'toolbars',
        ];
    }

    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getAttributes()
    {
        return [
            'style' => 'height: 200px;',
        ];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.quilljs.com/1.3.6/quill.bubble.css',
            '//cdn.quilljs.com/1.3.6/quill.snow.css',
        ];
    }

    public static function styles($id, $options)
    {
        $darkTheme = '';

        if (! isset($options['theme']) || (is_bool($options['theme']) && $options['theme'])) {
            $darkTheme = <<<CSS
    @media (prefers-color-scheme: dark) {
        .ql-container.ql-snow {
            border: 1px solid #111;
        }

        .ql-toolbar.ql-snow {
            border: 1px solid #000;
            background-color: #000;
        }

        .ql-toolbar.ql-snow .ql-fill {
            fill: #EEE !important;
        }

        .ql-snow .ql-stroke {
            stroke: #EEE !important;
        }

        .ql-editor hr {
            background-color: #FFF;
            height: 3px;
        }

        .ql-snow .ql-picker-label {
            color: #EEE !important;
        }

        .ql-snow .ql-picker-options {
            background-color: #222;
        }

        .ql-snow .ql-picker-options span {
            color: #EEE;
        }

        .ql-toolbar.ql-snow .ql-formats button i.fa {
            color: #EEE !important;
        }

        .ql-bubble .ql-editor {
            border: 1px solid transparent;
        }

        .ql-editor {
            background-color: #111;
            border: 1px solid transparent;
        }
        .ql-bubble .ql-editor code {
            background-color: #333;
        }
        .ql-bubble .ql-editor pre.ql-syntax {
            background-color: #333 !important;
            color: #FFF !important;
        }
    }
CSS;
        }

        if (isset($options['theme']) && is_string($options['theme']) && $options['theme'] === 'dark') {
            $darkTheme = <<<CSS
    .ql-container.ql-snow {
        border: 1px solid #111;
    }

    .ql-toolbar.ql-snow {
        border: 1px solid #000;
        background-color: #000;
    }

    .ql-toolbar.ql-snow .ql-fill {
        fill: #EEE !important;
    }

    .ql-editor hr {
        background-color: #FFF;
        height: 3px;
    }

    .ql-snow .ql-picker-label {
        color: #EEE !important;
    }

    .ql-snow .ql-picker-options {
        background-color: #222 !important;
    }

    .ql-toolbar.ql-snow .ql-formats button i.fa {
        color: #EEE !important;
    }

    .ql-picker-options span {
        color: #EEE;
    }

    .ql-snow .ql-stroke {
        stroke: #EEE !important;
    }

    .ql-bubble .ql-editor {
        border: 1px solid transparent;
    }

    .ql-editor {
        background-color: #111;
        border: 1px solid transparent;
    }
CSS;
        }

        return <<<CSS
    .ql-container {
        font-size: 16px;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .ql-toolbar.ql-snow {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        background-color: #FFF;
    }

    .ql-editor {
        padding: 24px;
        border-radius: 8px;
    }

    .ql-bubble .ql-editor {
        border: 1px solid #CCC;
    }

    .ql-bubble .ql-editor code {
        font-size: 100% !important;
        padding: 6px !important;
    }

    .ql-snow .ql-editor {
        border-radius: 0px;
    }

    .ql-snow .ql-color-picker .ql-picker-label svg, .ql-snow .ql-icon-picker .ql-picker-label svg {
        vertical-align: top;
    }

    .ql-editor hr {
        height: 3px;
    }

    .ql-bubble .ql-tooltip-editor input[type=text] {
        height: 40px;
    }

    .ql-bubble .ql-toolbar .ql-formats button i.fa {
        color: #EEE !important;
    }

    .ql-bubble .ql-editor pre.ql-syntax {
        background-color: #f0f0f0;
        color: #111;
        border-radius: 12px;
        padding: 24px;
    }

    .ql-editor ul[data-checked="true"] li::before, .ql-editor ul[data-checked="false"] li::before {
        font-size: 26px;
    }
    .ql-editor ul li::before {
    }
    .ql-editor ul[data-checked="true"] li {
        text-decoration: line-through;
    }

    {$darkTheme}
CSS;
    }

    public static function scripts($options)
    {
        return [
            '//cdn.quilljs.com/1.3.6/quill.js',
            '//cdn.jsdelivr.net/npm/quilljs-markdown@latest/dist/quilljs-markdown.js',
        ];
    }

    public static function getTemplate($options)
    {
        return <<<HTML
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div id="{id}_Editor"></div>
        {field}
        {errors}
    </div>
</div>
HTML;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_quillField';
    }

    public static function onLoadJsData($id, $options)
    {
        $route = null;

        if (isset($options['upload_route'])) {
            $route = route($options['upload_route']);
        }

        $theme = $options['quill_theme'] ?? 'snow';
        $placeholder = $options['placeholder'] ?? '';
        $toolbars = $options['toolbars'] ?? [
            'basic',
            'extra',
            'lists',
            'super_sub',
            'indents',
            'headers',
            'colors',
            'image',
            'video',
        ];

        $toolbars = collect($toolbars);

        throw_if($toolbars->isEmpty(), new \Exception('You cannot have an empty toolbar.'));

        if (is_null($route) && $toolbars->contains('image')) {
            throw new \Exception('You need to set an `upload_route` for handling image uploads to Quill.', 1);
        }

        $container = [
            ($toolbars->contains('basic')) ? ['bold', 'italic', 'underline', 'strike', ['align' => []], 'link'] : [],
            ($toolbars->contains('extra')) ? ['blockquote', 'code-block', 'divider'] : [],
            ($toolbars->contains('lists')) ? [['list' => 'ordered'], ['list' => 'bullet'], ['list' => 'check']] : [],
            ($toolbars->contains('super_sub')) ? [['script' => 'sub'], ['script' => 'super']] : [],
            ($toolbars->contains('indents')) ? [['indent' => '-1', 'indent' => '+1']] : [],
            ($toolbars->contains('headers')) ? [['header' => [1, 2, 3, 4, 5, 6, false]]] : [],
            ($toolbars->contains('colors')) ? [['color' => []], ['background' => []]] : [],
            ($toolbars->contains('image')) ? ['image'] : [],
            ($toolbars->contains('video')) ? ['video'] : [],
        ];

        return json_encode([
            'route' => $route,
            'theme' => $theme,
            'placeholder' => $placeholder,
            'container' => $container,
            'markdown' => $options['quill_markdown'] ?? false,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
            _formsjs_quillField = function (element) {
                if (! element.getAttribute('data-formsjs-rendered')) {
                    let _id = element.getAttribute('id');
                    let _instance = '_formsjs_'+ _id + '_Quill';
                    let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                    let _editor_icons = Quill.import('ui/icons');
                        _editor_icons['divider'] = '<i class="fa fa-horizontal-rule" aria-hidden="true"></i>';

                    let _editor_toolbarOptions = {
                        icons: _editor_icons,
                        container: _config.container,
                        handlers: {
                            image: function () {
                                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                                let _FileInput = this.container.querySelector('input.ql-image[type=file]');

                                if (_FileInput == null) {
                                    _FileInput = document.createElement('input');
                                    _FileInput.setAttribute('type', 'file');
                                    _FileInput.setAttribute('accept', 'image/png, image/gif, image/jpeg, image/bmp, image/x-icon');
                                    _FileInput.classList.add('ql-image');
                                    _FileInput.addEventListener('change', () => {
                                        const files = _FileInput.files;
                                        const range = this.quill.getSelection(true);

                                        if (!files || !files.length) {
                                            console.log('No files selected');
                                            return;
                                        }

                                        const _FileFormData = new FormData();
                                        _FileFormData.append('image', files[0]);

                                        this.quill.enable(false);

                                        window.axios
                                            .post(_config.route, _FileFormData)
                                            .then(response => {
                                                this.quill.enable(true);
                                                let range = this.quill.getSelection(true);
                                                this.quill.editor.insertEmbed(range.index, 'image', response.data.file.url);
                                                this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
                                                _FileInput.value = '';
                                            })
                                            .catch(error => {
                                                console.log('Image upload failed');
                                                console.log(error);
                                                this.quill.enable(true);
                                            });
                                    });
                                    this.container.appendChild(_FileInput);
                                }
                                _FileInput.click();
                            },
                            'divider': function (value) {
                                let range = window[_instance].getSelection(true);
                                window[_instance].insertEmbed(range.index + 1, 'divider', true, Quill.sources.USER);
                            }
                        }
                    };

                    if (! BlockEmbed) {
                        var BlockEmbed = Quill.import('blots/block/embed');
                        class DividerBlot extends BlockEmbed { }
                            DividerBlot.blotName = 'divider';
                            DividerBlot.tagName = 'hr';

                        Quill.register(DividerBlot);
                    }

                    window[_instance] = new Quill('#'+_id+'_Editor', {
                        theme: _config.theme,
                        placeholder: _config.placeholder,
                        modules: {
                            toolbar: _editor_toolbarOptions
                        }
                    });

                    if (_config.markdown) {
                        new QuillMarkdown(window[_instance]);
                    }

                    document.getElementById(_id+'_Editor').firstChild.innerHTML = element.value;
                    window[_instance].on('editor-change', function () {
                        element.value = document.getElementById(_id+'_Editor').firstChild.innerHTML;
                        let event = new Event('change', { 'bubbles': true });
                        element.dispatchEvent(event);
                    });

                    if (element.disabled) {
                        window[_instance].enable(false)
                    }
                }
            };
JS;
    }
}
