<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class CustomFile extends Field
{
    protected static function getType()
    {
        return 'custom-file';
    }

    protected static function getOptions()
    {
        return [];
    }

    protected static function getAttributes()
    {
        return [
            'data-formsjs-onchange' => 'FormJS_customfileField(event)'
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

    public static function js($id, $options)
    {
        return <<<JS
            window.FormJS_customfileField = function (input) {
                input.nextElementSibling.innerHTML = input.files[0].name;
            }
JS;
    }
}
