<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Flatpickr extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getOptions()
    {
        return [
            'before' => 'Date/Time',
        ];
    }

    protected static function getFactory()
    {
        return 'date';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/flatpickr',
        ];
    }

    public static function styles($id, $options)
    {
        return <<<CSS
@media (prefers-color-scheme: dark) {
    .flatpickr-calendar.open {
        background: #333;
        overflow: hidden;
    }

    .flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month {
        color: #FFF;
        fill: #FFF;
    }

    .flatpickr-months .flatpickr-month {
        color: #FFF;
        fill: #FFF;
    }

    .flatpickr-current-month .numInputWrapper span.arrowUp:after {
        border-bottom-color: #FFF;
    }

    .flatpickr-current-month .numInputWrapper span.arrowDown:after {
        border-top-color: #FFF;
    }

    span.flatpickr-weekday {
        color: rgb(160, 160, 160);
    }

    .flatpickr-day {
        color: rgb(160, 160, 160);
    }

    .flatpickr-day:hover {
        color: rgb(0, 0, 0);
    }

    .flatpickr-time .numInputWrapper span.arrowUp:after {
        border-bottom-color: #FFF;
    }

    .flatpickr-time .numInputWrapper span.arrowDown:after {
        border-top-color: #FFF;
    }

    .flatpickr-time .flatpickr-time-separator, .flatpickr-time .flatpickr-am-pm {
        color: #FFF;
    }

    .flatpickr-time input {
        color: #FFF;
    }

    .flatpickr-time input:hover, .flatpickr-time .flatpickr-am-pm:hover, .flatpickr-time input:focus, .flatpickr-time .flatpickr-am-pm:focus {
        background: #666;
        color: #FFF;
    }

    .flatpickr-calendar.arrowTop:before {
        border-bottom-color: #111;
    }
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_FlatpickrField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'enableTime' => $options['enableTime'] ?? 'true',
            'format' => $options['format'] ?? 'Y-m-j h:i K',
            'identity' => $options['identity'] ?? $id,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_FlatpickrField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                let _Flatpickr = flatpickr('#' + element.getAttribute('id'), {
                    id: _config.identity,
                    enableTime: _config.enableTime,
                    dateFormat: _config.format,
                });
            }
        }
JS;
    }
}
