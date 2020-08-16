<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Quill extends Field
{
    protected static function fieldOptions()
    {
        return [
            'theme',
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
            'style' => 'height: 200px;'
        ];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    protected static function stylesheets($options)
    {
        return [
            "//cdn.quilljs.com/1.3.6/quill.bubble.css",
            "//cdn.quilljs.com/1.3.6/quill.snow.css",
        ];
    }

    protected static function styles($id, $options)
    {
        $theme = $options['theme'] ?? 'light';
        $darkTheme = '';

        if ($theme == 'dark') {
            $darkTheme = <<<EOT
    .ql-container.ql-snow {
        border: 1px solid #111;
    }

    .ql-toolbar.ql-snow {
        border: 1px solid #000;
        background-color: #000;
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
    }

    .ql-editor {
        padding: 24px;
        border-radius: 8px;
    }

    .ql-bubble .ql-editor {
        border: 1px solid #CCC;
    }

    .ql-snow .ql-editor {
        border-radius: 0px;
    }

    .ql-snow .ql-color-picker .ql-picker-label svg, .ql-snow .ql-icon-picker .ql-picker-label svg {
        vertical-align: top;
    }

    {$darkTheme}
EOT;
    }

    protected static function scripts($options)
    {
        return ['//cdn.quilljs.com/1.3.6/quill.js'];
    }

    protected static function getTemplate()
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

    protected static function js($id, $options)
    {
        $route = request()->url();

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

        throw_if ($toolbars->isEmpty(), new \Exception('You cannot have an empty toolbar.'));

        $basic = ($toolbars->contains('basic')) ? "['bold', 'italic', 'underline', 'strike', { 'align': [] }, 'link']," : '';
        $extra = ($toolbars->contains('extra')) ? "['blockquote', 'code-block']," : '';
        $lists = ($toolbars->contains('lists')) ? "[{ 'list': 'ordered'}, { 'list': 'bullet' }]," : '';
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
                    _{$id}FileFormData.append('file', files[0]);

                    this.quill.enable(false);

                    window.axios
                        .post('$route', _{$id}FileFormData)
                        .then(response => {
                            this.quill.enable(true);
                            let range = this.quill.getSelection(true);
                            this.quill.editor.insertEmbed(range.index, 'image', response.data.data.path);
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

        return <<<EOT
var _editor_{$id}_toolbarOptions = {
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
        image: {$uploader}
    }
};

var {$id}_Quill = new Quill('#{$id}_Editor', {
    theme: '$theme',
    placeholder: '$placeholder',
    modules: {
        toolbar: _editor_{$id}_toolbarOptions
    }
});

document.getElementById('{$id}_Editor').firstChild.innerHTML = document.getElementById('{$id}').value;
{$id}_Quill.on('editor-change', function () {
    document.getElementById('{$id}').value = document.getElementById('{$id}_Editor').firstChild.innerHTML;
});
EOT;
    }
}
