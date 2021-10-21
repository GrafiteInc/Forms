<?php

namespace Grafite\Forms\Fields;

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
            '//unpkg.com/js-datepicker/dist/datepicker.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//unpkg.com/js-datepicker',
            '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
        ];
    }

    public static function styles($id, $options)
    {
        return <<<EOT
:root {
    --datepicker-bg-color: #111;
    --datepicker-color: #FFF;
    --datepicker-number-color: #FFF;
    --datepicker-header-color: var(--primary, "#EEE");
    --datepicker-highlight-color: var(--primary, "#EEE");
}

@media (prefers-color-scheme: light) {
    --datepicker-bg-color: #FFF;
    --datepicker-color: #FFF;
    --datepicker-number-color: #111;
    --datepicker-header-color: var(--primary, "#EEE");
    --datepicker-highlight-color: var(--primary, "#EEE");
}

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
EOT;
    }

    public static function js($id, $options)
    {
        $startDay = $options['start-day'] ?? 1;
        $format = $options['format'] ?? 'YYYY-MM-DD';
        $event = $options['event'] ?? 'keydown';
        $wait = $options['wait'] ?? 350;
        $identity = $options['identity'] ?? $id;

        return <<<EOT
var _{$id}Datepicker = datepicker("#${id}", {
  startDay: ${startDay},
  id: "${identity}",
  dateSelected: moment(document.getElementById("${id}").value, "${format}").toDate(),
  formatter: (input, date, instance) => {
      input.value = moment(date).format("${format}");
  }
});

const {$id}_datepicker_debounce = (func, wait) => {
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

const {$id}_debounce = {$id}_datepicker_debounce(function() {
    _{$id}Datepicker.hide();
	let date = moment(document.getElementById("${id}").value, "${format}").toDate();
    _{$id}Datepicker.setDate(date, true);
}, {$wait});

document.getElementById("${id}").addEventListener('{$event}', {$id}_debounce);
EOT;
    }
}
