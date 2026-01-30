<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

class Select2 extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'form-select',
            'multiple' => true,
            'data-size' => 8,
        ];
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            '//cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        ];
    }

    public static function styles($id, $options)
    {
        return <<<'CSS'
@media (prefers-color-scheme: dark) {
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #FFF;
    }

    .select2-container--bootstrap-5 .select2-selection {
        background-color: var(--app-input-bg, var(--bs-tertiary-bg));
        border: 1px solid var(--bs-border-color);
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
        background-color: var(--app-input-bg, var(--bs-tertiary-bg));
        border: 1px solid var(--bs-border-color);
        color: #FFF;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--selected, .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option[aria-selected=true]:not(.select2-results__option--highlighted) {
        background-color: var(--bs-primary);
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--highlighted {
        background-color: var(--bs-primary);
        color: #FFF;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        background-color: var(--app-input-bg, var(--bs-tertiary-bg));
        color: #FFF;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border: 1px solid var(--bs-border-color);
    }
}

@media (prefers-color-scheme: light) {
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: inherit !important;
    }
}

.select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--selected, .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option[aria-selected=true]:not(.select2-results__option--highlighted) {
    background-color: var(--bs-primary);
}
.select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--highlighted {
    background-color: var(--bs-primary);
    color: #FFF;
}
.select2-container--bootstrap-5.select2-container--focus .select2-selection, .select2-container--bootstrap-5.select2-container--open .select2-selection {
    box-shadow: none !important;
    border-color: inherit !important;
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_bootstrapSelect2Field';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'searchable' => (isset($options['searchable']) && $options['searchable'] === true) ?? false,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<'JS'
        window._formsjs_bootstrapSelect2Field = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                let _id = element.getAttribute('id');

                $(element).select2({
                    minimumResultsForSearch: _config.searchable ? 3 : Infinity,
                    theme: "bootstrap-5",
                });

                $(element).on('select2:select', function (e) {
                    let _id = $(element).closest('form').attr('id');
                    let _event = new Event('change', { 'bubbles': true });

                    document.querySelector('#'+_id).dispatchEvent(_event);
                });
            }
        }
JS;
    }
}
