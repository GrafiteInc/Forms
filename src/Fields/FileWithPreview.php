<?php

namespace Grafite\Forms\Fields;

use Illuminate\Support\Str;
use Grafite\Forms\Fields\Field;

class FileWithPreview extends Field
{
    protected static function getType()
    {
        return 'custom-file';
    }

    protected static function getAttributes()
    {
        return [
            'onChange' => 'window.FormMaker_previewFileUpload(this);'
        ];
    }

    protected static function getFactory()
    {
        return 'image';
    }

    public static function getTemplate($options)
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

    public static function js($id, $options)
    {
        $preview = $options['preview_identifier'] ?? '';
        $asBackgroundImage = $options['preview_as_background_image'] ?? false;

        $method = 'document.querySelector("' . $preview . '").setAttribute(\'src\', e.target.result);';

        if ($asBackgroundImage) {
            $method = 'document.querySelector("'.$preview.'").setAttribute(\'style\', "background-image: url("+e.target.result+")");';
        }

        $siblingCode = '';

        if (! Str::of(config('forms.bootstrap-version'))->startsWith('5')) {
            $siblingCode = 'input.nextElementSibling.innerHTML = input.files[0].name;';
        }

        return <<<EOT
window.FormMaker_previewFileUpload = function (input) {
    {$siblingCode}
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            ${method}
        };

        reader.readAsDataURL(input.files[0]);
    }
}
EOT;
    }
}
