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
        return [];
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
        return <<<EOT
<div class="dropzone-wrapper">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div id="{id}DropZone" class="dropzone"></div>
    </div>
    {errors}
</div>
EOT;
    }

    public static function styles($id, $options)
    {
        $darkTheme = '';

        if (! isset($options['theme']) || (is_bool($options['theme']) && $options['theme'])) {
            $darkTheme = <<<EOT
@media (prefers-color-scheme: dark) {
    .dropzone {
        border-radius: 4px;
        border: 1px solid #333;
        background-color: #111;
    }
}
EOT;
        }

        if (isset($options['theme']) && is_string($options['theme']) && $options['theme'] === 'dark') {
            $darkTheme = <<<EOT
    .dropzone {
        border-radius: 4px;
        border: 1px solid #333;
        background-color: #111;
    }
EOT;
        }

        return <<<EOT
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
EOT;
    }

    public static function js($id, $options)
    {
        $onComplete = $options['queue-complete'] ?? 'function () { window.location.reload() }';
        $multiple = $options['upload-muliple'] ?? 'true';
        $route = $options['route'] ?? '';
        $url = route($route);
        $token = csrf_token();

        return <<<EOT
Dropzone.autoDiscover = false;
new Dropzone("#{$id}DropZone", {
    url: "$url",
    uploadMultiple: $multiple,
    sending: function(file, xhr, formData) {
        formData.append("_token", "$token");
    },
    queuecomplete: $onComplete,
});
EOT;
    }
}
