<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

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

    /**
     * Field maker options
     *
     * @return array
     */
    protected static function getOptions()
    {
        return [];
    }

    protected static function stylesheets($options)
    {
        return [
            "//cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.0/jquery.typeahead.min.css",
        ];
    }

    protected static function scripts($options)
    {
        return [
            '//cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.0/jquery.typeahead.min.js'
        ];
    }

    protected static function getTemplate()
    {
        return <<<EOT
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div class="typeahead__container">
            <div class="typeahead__field">
                <div class="typeahead__query">{field}</div>
            </div>
        </div>
    </div>
    {errors}
</div>
EOT;
    }

    protected static function js($id, $options)
    {
        $values = $options['matches'];

        return <<<EOT
$.typeahead({
    input: '.typeahead',
    order: "desc",
    source: {
        data: $values
    }
});
EOT;
    }
}
