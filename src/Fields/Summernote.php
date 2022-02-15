<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

class Summernote extends Field
{
    protected static function fieldOptions()
    {
        return [
            // 'quill_theme',
            // 'toolbars',
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
            '//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css',
        ];
    }

    public static function styles($id, $options)
    {
        return '';
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js',
        ];
    }

    public static function getTemplate($options)
    {
        return <<<EOT
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div id="{id}"></div>
        {field}
        {errors}
    </div>
</div>
EOT;
    }

    public static function js($id, $options)
    {
        return <<<EOT
$('#${id}').summernote({
  height: 300,
  minHeight: null,
  maxHeight: null,
  toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
});
EOT;
    }
}
