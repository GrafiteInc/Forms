<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Address extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function stylesheets($options)
    {
        return [
            "//unpkg.com/@geoapify/geocoder-autocomplete@^1/styles/minimal.css",
        ];
    }

    public static function scripts($options)
    {
        return [
            "//unpkg.com/@geoapify/geocoder-autocomplete@^1/dist/index.min.js",
        ];
    }

    public static function styles($id, $options)
    {
        return <<<CSS
            .geoapify-autocomplete-input {
                border: none;
                padding: 0;
                height: auto;
                line-height: inherit;
                font-size: inherit;
                background-color: transparent;
            }

            .geoapify-autocomplete-items {
                position: absolute;
                border: 1px solid rgba(0, 0, 0, 0.4);
                border-top: none;
                background-color: var(--bs-tertiary-bg);
                border-bottom-right-radius: 4px;
                border-bottom-left-radius: 4px;
                margin-top: 2px;
            }
CSS;
    }

    public static function getTemplate($options)
    {
        return <<<HTML
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div id="{id}_autocompleteAddress" class="position-relative form-control"></div>
    <div class="{fieldClass}">
        {field}
    </div>
    {errors}
</div>
HTML;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_addressGeoapifyField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'key' => $options['key'] ?? null,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_addressGeoapifyField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _id = element.getAttribute('id');
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                window[_id+'_autocomplete'] = new autocomplete.GeocoderAutocomplete(
                    document.getElementById(_id+'_autocompleteAddress'),
                    _config.key,
                    {
                        allowNonVerifiedHouseNumber: true,
                        allowNonVerifiedStreet: true,
                        skipDetails: true,
                        skipIcons: true,
                    });

                window[_id+'_autocomplete'].on('select', (street) => {
                    element.value = JSON.stringify(street.properties);
                });
            }
        }
JS;
    }
}
