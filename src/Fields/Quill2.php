<?php

namespace Grafite\Forms\Fields;

class Quill2 extends Field
{
    protected static function fieldOptions()
    {
        return [
            'mention_ats',
            'mention_hashes',
            'mention_links',
            'mention_link_path',
            'mention_at_path',
            'mention_hash_path',
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
            '//cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.bubble.css',
            '//cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css',
            '//cdn.jsdelivr.net/npm/quill-mention@3.4.0/dist/quill.mention.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js',
            '//cdn.jsdelivr.net/npm/quilljs-markdown@latest/dist/quilljs-markdown.js',
            '//cdn.jsdelivr.net/npm/quill-image-drop-and-paste@2.0.1/dist/quill-image-drop-and-paste.min.js',
            '//cdn.jsdelivr.net/gh/hunghg255/quill-resize-module/dist/quill-resize-image.min.js',
            '//cdn.jsdelivr.net/npm/quill-mention@3.4.0/dist/quill.mention.min.js',
            '//cdn.jsdelivr.net/npm/quill-magic-url@4.2.0/dist/index.min.js',
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
        .ql-container .ql-mention-list-container {
            background-color: #000 !important;
        }
        .ql-mention-list-item.selected {
            color: #fff;
            background-color: var(--bs-primary) !important;
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

    .ql-bubble .ql-editor .ql-code-block {
        font-size: 100% !important;
        padding: 6px !important;
    }

    .ql-snow .ql-editor {
        border-radius: 0px;
    }

    .ql-snow .ql-color-picker .ql-picker-label svg, .ql-snow .ql-icon-picker .ql-picker-label svg {
        vertical-align: top;
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

    .ql-editor li[data-list="checked"] span::before, .ql-editor li[data-list="unchecked"] span::before {
        font-size: 26px;
        margin-top: -4px;
        line-height: 28px;
    }

    .ql-editor li[data-list="checked"] {
        text-decoration: line-through;
        color: var(--bs-primary);
    }
    .ql-editor li[data-list="checked"] span:before {
        color: var(--bs-primary);
    }

    .ql-editor .mention {
        background-color: var(--bs-primary);
        color: var(--bs-white);
        cursor: pointer;
    }

    {$darkTheme}
CSS;
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

        $mentionAtPath = $options['mention_at_path'] ?? '{at}';
        $mentionHashPath = $options['mention_hash_path'] ?? '{hash}';
        $mentionLinkPath = $options['mention_link_path'] ?? '{link}';

        $mentions = $options['mention_ats'] ?? [];
        $hashValues = $options['mention_hashes'] ?? [];
        $links = $options['mention_links'] ?? [];
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
            ($toolbars->contains('extra')) ? ['blockquote', 'code-block'] : [],
            ($toolbars->contains('lists')) ? [['list' => 'ordered'], ['list' => 'bullet'], ['list' => 'check']] : [],
            ($toolbars->contains('super_sub')) ? [['script' => 'sub'], ['script' => 'super']] : [],
            ($toolbars->contains('indents')) ? [['indent' => '-1', 'indent' => '+1']] : [],
            ($toolbars->contains('headers')) ? [['header' => [1, 2, 3, 4, 5, 6, false]]] : [],
            ($toolbars->contains('colors')) ? [['color' => []], ['background' => []]] : [],
            ($toolbars->contains('image')) ? ['image'] : [],
            ($toolbars->contains('video')) ? ['video'] : [],
            ['clean']
        ];

        return json_encode([
            'route' => $route,
            'theme' => $theme,
            'mention_at_path' => $mentionAtPath,
            'mention_hash_path' => $mentionHashPath,
            'mention_link_path' => $mentionLinkPath,
            'atValues' => $mentions,
            'hashValues' => $hashValues,
            'linkValues' => $links,
            'placeholder' => $placeholder,
            'container' => $container,
            'markdown' => $options['quill_markdown'] ?? false,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
            window._formsjs_quillField = function (element) {
                element.addEventListener('grafite-form-change', function (event) {
                    let _method = element.form.getAttribute('data-formsjs-onchange');
                        _method = _method.replace('(event)', '');
                    window[_method](event);
                });

                if (! element.getAttribute('data-formsjs-rendered')) {
                    let _id = element.getAttribute('id');
                    let _instance = '_formsjs_'+ _id + '_Quill';
                    let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                    let QuillImageData = QuillImageDropAndPaste.ImageData;

                    window._formsjs_quill_image_upload = function (route, formData, quill, range) {
                        window.axios
                            .post(route, formData)
                            .then(response => {
                                quill.enable(true);
                                quill.editor.insertEmbed(range.index, 'image', response.data.file.url);
                                quill.setSelection(range.index + 1, Quill.sources.SILENT);
                                _FileInput.value = '';
                            })
                            .catch(error => {
                                console.log('Image upload failed');
                                console.log(error);
                                quill.enable(true);
                            });
                    }

                    window._formsjs_quill_image_process = function (dataUrl, type, imageData) {
                        let _container = null;
                        if (this.constructor.name.includes('Keyboard')) {
                            _container = this.quill.getModule('toolbar').container;
                        } else {
                            _container = this.quill.container;
                        }

                        let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                        let _FileInput = _container.querySelector('input.ql-image[type=file]');

                        if (_FileInput == null) {
                            _FileInput = document.createElement('input');
                            _FileInput.setAttribute('type', 'file');
                            _FileInput.setAttribute('accept', 'image/png, image/gif, image/jpeg, image/bmp, image/x-icon');
                            _FileInput.classList.add('ql-image');
                            _FileInput.classList.add('ql-hidden');
                            _FileInput.addEventListener('change', (e) => {
                                const files = _FileInput.files;
                                const range = this.quill.getSelection(true);

                                if (! files || ! files.length) {
                                    console.log('No files selected');
                                    return;
                                }

                                let _FileFormData = new FormData();
                                    _FileFormData.append('image', files[0]);

                                this.quill.enable(false);

                                window._formsjs_quill_image_upload(
                                    _config.route,
                                    _FileFormData,
                                    this.quill,
                                    range
                                );
                            });

                            if (imageData.dataUrl) {
                                var file = imageData.toFile('quill-upload-image.png');

                                let _FileFormData = new FormData();
                                    _FileFormData.append('image', file);

                                window._formsjs_quill_image_upload(
                                    _config.route,
                                    _FileFormData,
                                    this.quill,
                                    this.quill.getSelection(true)
                                );
                            } else {
                                _container.appendChild(_FileInput);
                                _FileInput.click();
                            }
                        }
                    }

                    let _editor_toolbarOptions = {
                        container: _config.container,
                        handlers: {
                            image: window._formsjs_quill_image_process,
                        }
                    };

                    let _route = _config.route;

                    window[_instance+'_atValues'] = _config.atValues;
                    window[_instance+'_hashtagValues'] = _config.hashValues;
                    window[_instance+'_linkValues'] = _config.linkValues;
                    window[_instance+'_originalValue'] = element.value;

                    Quill.register("modules/resize", window.QuillResizeImage);

                    window[_instance] = new Quill('#'+_id+'_Editor', {
                        theme: _config.theme,
                        placeholder: _config.placeholder,
                        modules: {
                            magicUrl: true,
                            toolbar: _editor_toolbarOptions,
                            imageDropAndPaste: {
                                handler: window._formsjs_quill_image_process,
                            },
                            resize: {
                                locale: {
                                    center: "center",
                                },
                            },
                            mention: {
                                allowedChars: /^[A-Za-z\sÅÄÖåäö]*$/,
                                mentionDenotationChars: ["@", "#", "^"],
                                source: function(searchTerm, renderList, mentionChar) {
                                    let values;

                                    if (mentionChar === "@") {
                                        values = window[_instance+'_atValues'];
                                    }

                                    if (mentionChar === "#") {
                                        values = window[_instance+'_hashtagValues'];
                                    }

                                    if (mentionChar === "^") {
                                        values = window[_instance+'_linkValues'];
                                    }

                                    if (searchTerm.length === 0) {
                                        renderList(values, searchTerm);
                                    } else {
                                        const matches = [];
                                        for (let i = 0; i < values.length; i++) {
                                            if (~values[i].value.toLowerCase().indexOf(searchTerm.toLowerCase())) {
                                                matches.push(values[i]);
                                            }

                                            renderList(matches, searchTerm);
                                        }
                                    }
                                }
                            },
                            keyboard: {
                                bindings: {
                                    image: {
                                        key: 'i',
                                        ctrlKey: true,
                                        handler: window._formsjs_quill_image_process,
                                    },
                                }
                            }
                        }
                    });

                    if (_config.markdown) {
                        new QuillMarkdown(window[_instance]);
                    }

                    document.getElementById(_id+'_Editor').firstChild.innerHTML = element.value;

                    window[_instance].on('editor-change', function () {
                        if (
                            document.getElementById(_id).getAttribute('disabled') !== 'disabled'
                            && document.getElementById(_id).getAttribute('readonly') !== 'readonly'
                            && document.getElementById(_id+'_Editor').firstChild.innerHTML !== window[_instance+'_originalValue']
                        ) {
                            element.value = document.getElementById(_id+'_Editor').firstChild.innerHTML;

                            let event = new CustomEvent('grafite-form-change', { 'bubbles': true });
                            element.dispatchEvent(event);
                        }
                    });

                    if (element.disabled) {
                        window[_instance].enable(false)
                    }

                    // window.addEventListener('mention-hovered', (event) => {console.log('hovered: ', event)}, false);

                    window.addEventListener('mention-clicked', function (event) {
                        if (event.value.denotationChar === '^') {
                            if (_config.mention_link_path !== '{link}') {
                                window.location = _config.mention_link_path.replace('{link}', event.value.id);
                            }
                        }

                        if (event.value.denotationChar === '@') {
                            if (_config.mention_at_path !== '{at}') {
                                window.location = _config.mention_at_path.replace('{at}', event.value.id);
                            }
                        }

                        if (event.value.denotationChar === '#') {
                            if (_config.mention_hash_path !== '{hash}') {
                                window.location = _config.mention_hash_path.replace('{hash}', event.value.id);
                            }
                        }
                    }, false);
                }
            };
JS;
    }
}
