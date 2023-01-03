<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Typeahead extends Field
{
    /**
     * Input type
     *
     * @return string
     */
    protected static function getType()
    {
        return 'text';
    }

    /**
     * Input attributes
     *
     * @return array
     */
    protected static function getAttributes()
    {
        return [
            'class' => 'typeahead form-control',
        ];
    }

    public static function stylesheets($options)
    {
        return [
            "//cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.0/jquery.typeahead.min.css",
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.0/jquery.typeahead.min.js'
        ];
    }

    public static function getTemplate($options)
    {
        return <<<EOT
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div class="typeahead__container">
            <div class="typeahead__field">
                <div class="typeahead__query">
                    {field}
                    {errors}
                </div>
            </div>
        </div>
    </div>
</div>
EOT;
    }

    public static function js($id, $options)
    {
        $values = $options['matches'];

        return <<<EOT
$.typeahead({
    input: '#{$id}',
    order: "desc",
    source: {
        data: {$values}
    }
});
EOT;
    }
}
