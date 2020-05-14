<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class FileWithPreview extends Field
{
    protected static function getType()
    {
        return 'file';
    }

    protected static function getAttributes()
    {
        return [
            'onChange' => 'window.FormMaker_previewFileUpload(this);'
        ];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    protected static function getTemplate()
    {
        return <<<EOT
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        {field}
    </div>
    {errors}
</div>
EOT;
    }

    protected static function js($id, $options)
    {
        $preview = $options['preview_identifier'] ?? '';
        $asBackgroundImage = $options['preview_as_background_image'] ?? false;

        $method = '$('.$preview.').attr(\'src\', e.target.result);';

        if ($asBackgroundImage) {
            $method = '$("'.$preview.'").attr(\'style\', "background-image: url("+e.target.result+")");';
        }

        return <<<EOT
window.FormMaker_previewFileUpload = function (input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $method
        };

        reader.readAsDataURL(input.files[0]);
    }
}
EOT;
    }
}
