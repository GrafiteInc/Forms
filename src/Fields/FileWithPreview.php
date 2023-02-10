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
            'onChange' => '_formsjs_fileWithPreviewField(this);'
        ];
    }

    protected static function getFactory()
    {
        return 'image';
    }

    public static function getTemplate($options)
    {
        return <<<HTML
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        {field}
    </div>
    {errors}
</div>
HTML;
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'preview' => $options['preview_identifier'] ?? '',
            'as_background_image' => $options['preview_as_background_image'] ?? false,
        ]);
    }

    public static function js($id, $options)
    {
        $siblingCode = '';

        if (! Str::of(config('forms.bootstrap-version'))->startsWith('5')) {
            $siblingCode = 'input.nextElementSibling.innerHTML = input.files[0].name;';
        }

        return <<<JS
            _formsjs_fileWithPreviewField = function (input) {
                let _config = JSON.parse(input.getAttribute('data-formsjs-onload-data'));
                let _method = function (e) { document.querySelector(_config.preview).setAttribute('src', e.target.result); };

                if (_config.as_background_image) {
                    _method = function (e) { document.querySelector(_config.preview).setAttribute('style', 'background-image: url('+e.target.result+')'); };
                }

                {$siblingCode}

                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) { _method(e) };

                    reader.readAsDataURL(input.files[0]);
                }
            }
JS;
    }
}
