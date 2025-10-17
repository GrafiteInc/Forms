<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

class TomSelect extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker',
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
            '//cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.min.css',
            '//cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.bootstrap5.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js',
        ];
    }

    public static function styles($id, $options)
    {
        return <<<CSS
:root {
    .ts-wrapper.form-control:not(.disabled) .ts-control, .ts-wrapper.form-control:not(.disabled).single.input-active .ts-control, .ts-wrapper.form-select:not(.disabled) .ts-control, .ts-wrapper.form-select:not(.disabled).single.input-active .ts-control {
        line-height: 1.5rem !important;
        padding: 10px 16px;
        background-color: var(--bs-tertiary-bg) !important;
    }
}

@media (prefers-color-scheme: dark) {
    :root {
        .ts-wrapper.form-select:not(.disabled) .ts-control, .ts-wrapper.form-select:not(.disabled).single.input-active .ts-control {
            color: var(--bs-white);
            background-color: var(--bs-tertiary-bg) !important;
        }

        .ts-control, .ts-wrapper.single.input-active .ts-control {
            background-color: var(--bs-tertiary-bg);
        }

        .ts-dropdown, .ts-dropdown.form-control, .ts-dropdown.form-select {
            background-color: var(--bs-tertiary-bg);
        }

        .ts-dropdown .option {
            color: var(--bs-white);
        }

        .ts-dropdown .option.active, .ts-dropdown .option.selected {
            background-color: var(--bs-dark);
        }

        .ts-control, .ts-control input, .ts-dropdown {
            color: var(--bs-white);
        }
    }
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_bootstrapTomSelectField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'btn' => $options['btn'] ?? 'btn-outline-primary',
            'with_add_item' => $options['add-item'] ?? false,
            'add_item_placeholder' => $options['add-item-placeholder'] ?? 'Add Item',
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        window._formsjs_bootstrapTomSelectField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _id = element.getAttribute('id');
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                if (element.getAttribute('data-formsjs-onchange')) {
                    _config['onChange'] = function (value) {
                        element.closest('form').submit();
                    }
                }

                new TomSelect(element, _config);
            }
        }
JS;
    }
}
