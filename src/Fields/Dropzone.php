<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Dropzone extends Field
{
    protected static function getType()
    {
        return 'file';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'dropzone'
        ];
    }

    protected static function getFactory()
    {
        return 'image';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/min/dropzone.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/min/dropzone.min.js',
        ];
    }

    public static function getTemplate($options)
    {
        return <<<HTML
<div class="dropzone-wrapper">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div id="{id}DropZone" {attributes} class="dropzone" ></div>
    </div>
    {errors}
</div>
HTML;
    }

    public static function styles($id, $options)
    {
        $darkTheme = '';

        if (! isset($options['theme']) || (is_bool($options['theme']) && $options['theme'])) {
            $darkTheme = <<<CSS
@media (prefers-color-scheme: dark) {
    .dropzone {
        border-radius: 4px;
        border: 1px solid #333;
        background-color: #111;
    }
}
CSS;
        }

        if (isset($options['theme']) && is_string($options['theme']) && $options['theme'] === 'dark') {
            $darkTheme = <<<CSS
    .dropzone {
        border-radius: 4px;
        border: 1px solid #333;
        background-color: #111;
    }
CSS;
        }

        return <<<CSS
.dropzone {
    border-radius: 4px;
    border: 1px solid #CCC;
    background-color: #FFF;
}

.dz-button {
    min-height: 50px;
}

.dropzone .dz-preview.dz-image-preview {
    background-color: transparent;
}

{$darkTheme}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_dropzoneField';
    }

    public static function onLoadJsData($id, $options)
    {
        $route = $options['route'] ?? '';

        return json_encode([
            'queue-complete' => '_formsjs_reload_page',
            'multiple' => $options['upload-muliple'] ?? 'true',
            'url' => route($route),
            'params' => $options['params'],
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_reload_page = function () {
            window.location.reload();
        }

        _formsjs_dropzoneField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                let _fieldId = element.getAttribute('id');
                Dropzone.autoDiscover = false;
                new Dropzone("#"+_fieldId, {
                    url: _config.url,
                    params: _config.params,
                    uploadMultiple: _config.multiple,
                    sending: function(file, xhr, formData) {
                        formData.append("_token", document.head.querySelector('meta[name="csrf-token"]').content);
                    },
                    queuecomplete: window[_config['queue-complete']],
                });
            }
        }
JS;
    }
}
