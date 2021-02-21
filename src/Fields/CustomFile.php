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
            'onChange' => 'window.FormMaker_customFile(this);'
        ];
    }

    protected static function getFactory()
    {
        return 'image';
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
        return <<<EOT
window.FormMaker_customFile = function (input) {
    input.nextElementSibling.innerHTML = input.files[0].name;
}
EOT;
    }
}
