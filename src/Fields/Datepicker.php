<?php

namespace Grafite\Forms\Fields;

use Illuminate\Support\Str;
use Grafite\Forms\Fields\Field;

class Datepicker extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getOptions()
    {
        return [
            'before' => 'Date',
        ];
    }

    protected static function getFactory()
    {
        return 'date';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/js-datepicker@5.18.2/dist/datepicker.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/js-datepicker@5.18.2/dist/datepicker.min.js',
            '//cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js',
        ];
    }

    public static function styles($id, $options)
    {
        $color = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? '--bs-primary' : '--primary';

        $darkTheme = '';

        if (! isset($options['theme']) || (is_bool($options['theme']) && $options['theme'])) {
            $darkTheme = <<<CSS
@media (prefers-color-scheme: dark) {
    :root {
        --datepicker-bg-color: #111;
        --datepicker-color: #FFF;
        --datepicker-number-color: #FFF;
        --datepicker-header-color: var($color, "#EEE");
        --datepicker-highlight-color: var($color, "#EEE");
    }
}
CSS;
        }

        if (isset($options['theme']) && is_string($options['theme']) && $options['theme'] === 'dark') {
            $darkTheme = <<<CSS
:root {
    --datepicker-bg-color: #111;
    --datepicker-color: #FFF;
    --datepicker-number-color: #FFF;
    --datepicker-header-color: var($color, "#EEE");
    --datepicker-highlight-color: var($color, "#EEE");
}
CSS;
        }

        return <<<CSS
:root {
    --datepicker-bg-color: #FFF;
    --datepicker-color: #FFF;
    --datepicker-number-color: #111;
    --datepicker-header-color: var($color, "#EEE");
    --datepicker-highlight-color: var($color, "#EEE");
}

{$darkTheme}

.qs-datepicker-container {
    color: var(--datepicker-number-color);
    background-color: var(--datepicker-bg-color);
}
.qs-datepicker .qs-controls {
    background-color: var(--datepicker-header-color);
    color: var(--datepicker-color);
    height: 35px;
}
.qs-datepicker .qs-square {
    height: 32px;
}
.qs-datepicker .qs-square.qs-active {
    background-color: var(--datepicker-header-color);
    color: var(--datepicker-color);
}
.qs-datepicker .qs-square:not(.qs-empty):not(.qs-disabled):not(.qs-day):not(.qs-active):hover {
    background-color: var(--datepicker-highlight-color);
    color: var(--datepicker-color);
}
.qs-datepicker .qs-arrow.qs-left:after {
    border-right-color: var(--datepicker-color);
}
.qs-datepicker .qs-arrow.qs-right:after {
    border-left-color: var(--datepicker-color);
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_DatepickerField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'startDay' => $options['start-day'] ?? 1,
            'format' => $options['format'] ?? 'YYYY-MM-DD',
            'event' => $options['event'] ?? 'keydown',
            'wait' => $options['wait'] ?? 850,
            'identity' => $options['identity'] ?? $id,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        window._formsjs_DatepickerField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                let _Datepicker = datepicker('#' + element.getAttribute('id'), {
                    startDay: _config.startDay,
                    id: _config.identity,
                    dateSelected: (element.value) ? moment(element.value, _config.format).toDate() : null,
                    formatter: (input, date, instance) => {
                        input.value = moment(date).format(_config.format);
                    }
                });

                let _datepicker_debounce = (func, wait) => {
                    let timeout;

                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };

                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                };

                let _debounce = _datepicker_debounce(function () {
                    _Datepicker.hide();
                    if (element.value !== '') {
                        let date = moment(element.value, _config.format).toDate();
                        _Datepicker.setDate(date, true);
                    }
                }, _config.wait);

                element.addEventListener(_config.event, _debounce);
            }
        }
JS;
    }
}
