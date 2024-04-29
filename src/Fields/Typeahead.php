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
            '//cdn.jsdelivr.net/npm/jquery-typeahead@2.11.1/dist/jquery.typeahead.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/jquery-typeahead@2.11.1/dist/jquery.typeahead.min.js',
        ];
    }

    public static function getTemplate($options)
    {
        return <<<HTML
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
HTML;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_typeaheadField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'matches' => json_decode($options['matches']),
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
            _formsjs_typeaheadField = function (element) {
                if (! element.getAttribute('data-formsjs-rendered')) {
                    let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                    $.typeahead({
                        input: '#' + element.getAttribute('id'),
                        order: "desc",
                        source: {
                            data: _config.matches
                        }
                    });
                }
            }
JS;
    }
}
