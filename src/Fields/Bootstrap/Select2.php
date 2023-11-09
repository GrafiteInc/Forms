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
            "//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css",
            "//cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css",
        ];
    }

    public static function scripts($options)
    {
        return [
            "//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js",
        ];
    }

    public static function styles($id, $options)
    {
        return <<<CSS
@media (prefers-color-scheme: dark) {
    .select2-container--bootstrap-5 .select2-selection {
        background-color: var(--bs-tertiary-bg);
        border: #111;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
        background-color: var(--bs-tertiary-bg);
        border: #111 1px solid;
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
        background-color: var(--bs-tertiary-bg);
        color: #FFF;
    }
}

.select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--selected, .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option[aria-selected=true]:not(.select2-results__option--highlighted) {
    background-color: var(--bs-primary);
}
.select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--highlighted {
    background-color: var(--bs-primary);
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

        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_bootstrapSelect2Field = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _id = element.getAttribute('id');

                $(element).select2({
                    theme: "bootstrap-5",
                });
            }
        }
JS;
    }
}
