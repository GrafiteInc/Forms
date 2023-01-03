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
            $darkTheme = <<<EOT
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
EOT;
        }

        if (isset($options['theme']) && is_string($options['theme']) && $options['theme'] === 'dark') {
            $darkTheme = <<<EOT
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
EOT;
        }

        return <<<EOT
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
EOT;
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
        return <<<EOT
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div id="{id}_Editor"></div>
        {field}
        {errors}
    </div>
</div>
EOT;
    }

    public static function js($id, $options)
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

        $basic = ($toolbars->contains('basic')) ? "['bold', 'italic', 'underline', 'strike', { 'align': [] }, 'link']," : '';
        $extra = ($toolbars->contains('extra')) ? "['blockquote', 'code-block', 'divider']," : '';
        $lists = ($toolbars->contains('lists')) ? "[{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }]," : '';
        $superSub = ($toolbars->contains('super_sub')) ? "[{ 'script': 'sub'}, { 'script': 'super' }]," : '';
        $indents = ($toolbars->contains('indents')) ? "[{ 'indent': '-1'}, { 'indent': '+1' }]," : '';
        $headers = ($toolbars->contains('headers')) ? "[{ 'header': [1, 2, 3, 4, 5, 6, false] }]," : '';
        $colors = ($toolbars->contains('colors')) ? "[{ 'color': [] }, { 'background': [] }]," : '';
        $image = ($toolbars->contains('image')) ? "['image']," : '';
        $video = ($toolbars->contains('video')) ? "['video']," : '';

        $defaultUploader = <<<EOT
        function () {
            let _{$id}FileInput = this.container.querySelector('input.ql-image[type=file]');

            if (_{$id}FileInput == null) {
                _{$id}FileInput = document.createElement('input');
                _{$id}FileInput.setAttribute('type', 'file');
                _{$id}FileInput.setAttribute('accept', 'image/png, image/gif, image/jpeg, image/bmp, image/x-icon');
                _{$id}FileInput.classList.add('ql-image');
                _{$id}FileInput.addEventListener('change', () => {
                    const files = _{$id}FileInput.files;
                    const range = this.quill.getSelection(true);

                    if (!files || !files.length) {
                        console.log('No files selected');
                        return;
                    }

                    const _{$id}FileFormData = new FormData();
                    _{$id}FileFormData.append('image', files[0]);

                    this.quill.enable(false);

                    window.axios
                        .post('{$route}', _{$id}FileFormData)
                        .then(response => {
                            this.quill.enable(true);
                            let range = this.quill.getSelection(true);
                            this.quill.editor.insertEmbed(range.index, 'image', response.data.file.url);
                            this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
                            _{$id}FileInput.value = '';
                        })
                        .catch(error => {
                            console.log('Image upload failed');
                            console.log(error);
                            this.quill.enable(true);
                        });
                });
                this.container.appendChild(_{$id}FileInput);
            }
            _{$id}FileInput.click();
        }
EOT;

        $uploader = $options['uploader'] ?? $defaultUploader;

        $markdown = (isset($options['quill_markdown']) && $options['quill_markdown']) ? "var {$id}_Quill_Markdown = new QuillMarkdown({$id}_Quill);" : '';

        return <<<EOT
var _editor_{$id}_icons = Quill.import('ui/icons');
    _editor_{$id}_icons['divider'] = '<i class="fa fa-horizontal-rule" aria-hidden="true"></i>';

var _editor_{$id}_dividerHandler = function (value) {
  let range = {$id}_Quill.getSelection(true);
  {$id}_Quill.insertEmbed(range.index + 1, 'divider', true, Quill.sources.USER);
}

var _editor_{$id}_toolbarOptions = {
    icons: _editor_{$id}_icons,
    container: [
        {$basic}
        {$extra}
        {$lists}
        {$superSub}
        {$indents}
        {$headers}
        {$colors}
        {$image}
        {$video}
        ['clean']
    ],
    handlers: {
        image: {$uploader},
        'divider': _editor_{$id}_dividerHandler
    }
};

if (! BlockEmbed) {
var BlockEmbed = Quill.import('blots/block/embed');
class DividerBlot extends BlockEmbed { }
    DividerBlot.blotName = 'divider';
    DividerBlot.tagName = 'hr';

Quill.register(DividerBlot);
}

var {$id}_Quill = new Quill('#{$id}_Editor', {
    theme: '{$theme}',
    placeholder: '{$placeholder}',
    modules: {
        toolbar: _editor_{$id}_toolbarOptions
    }
});

{$markdown}

document.getElementById('{$id}_Editor').firstChild.innerHTML = document.getElementById('{$id}').value;
{$id}_Quill.on('editor-change', function () {
    document.getElementById('{$id}').value = document.getElementById('{$id}_Editor').firstChild.innerHTML;
    let event = new Event('change', { 'bubbles': true });
    document.getElementById('{$id}').dispatchEvent(event);
});

if (document.getElementById('{$id}').disabled) {
    {$id}_Quill.enable(false)
}
EOT;
    }
}
