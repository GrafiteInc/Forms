<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Illuminate\Support\Str;
use Grafite\Forms\Fields\Field;

class DateTimePicker extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getOptions()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'date';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.0.0-beta1/dist/css/tempus-dominus.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js',
            '//cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.0.0-beta1/dist/js/tempus-dominus.min.js',
            '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
        ];
    }

    public static function styles($id, $options)
    {
        $color = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? '--bs-primary' : '--primary';

        $darkTheme = '';

        if (! isset($options['theme']) || (is_bool($options['theme']) && $options['theme'])) {
            $darkTheme = <<<EOT
@media (prefers-color-scheme: dark) {
    .tempus-dominus-widget {
        border: 2px solid #333;
    }

    .tempus-dominus-widget .date-container-days .day:hover,
    .tempus-dominus-widget .date-container-months .month:hover,
    .tempus-dominus-widget .date-container-years .year:hover,
    .tempus-dominus-widget .time-container .time-container-hour .hour {
        background-color: #333 !important;
    }

    .time-container-clock div:hover {
        background-color: #333 !important;
    }

    .tempus-dominus-widget .day.new, .tempus-dominus-widget .day.old {
        color: #666 !important;
    }
    .tempus-dominus-widget .dow.no-highlight {
        color: #333;
    }
    .tempus-dominus-widget .toolbar div:first-child:hover {
        background-color: #333;
    }

    .tempus-dominus-widget {
        background-color: #111;
    }
}
EOT;
        }

        return <<<EOT
        .tempus-dominus-widget {
            z-index: 90000;
        }
${darkTheme}
EOT;
    }

    public static function js($id, $options)
    {
        $defaultDateCode = '';
        $format = $options['format'] ?? 'LLLL';
        $defaultDate = $options['defaultDate'] ?? false;

        if ($defaultDate) {
            $defaultDateCode = 'defaultDate: "' . $defaultDate . '",';
        }

        return <<<EOT
 new tempusDominus.TempusDominus(document.getElementById('${id}'), {
     ${defaultDateCode}
     hooks: {
         inputFormat:(context, date) => {
             return moment(date).format('${format}')
        }
     }
 });
EOT;
    }
}
