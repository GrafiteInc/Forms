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

    protected static function stylesheets($options)
    {
        return [
            '//unpkg.com/js-datepicker/dist/datepicker.min.css',
        ];
    }

    protected static function scripts($options)
    {
        return [
            '//unpkg.com/js-datepicker',
            '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
        ];
    }

    protected static function styles($id, $options)
    {
        $theme = $options['theme'] ?? 'light';

        $background = $options['background-color'] ?? '#FFF';
        $color = $options['color'] ?? '#FFF';
        $numberColor = $options['number-color'] ?? '#111';
        $highlight = $options['highlight'] ?? 'var(--primary, "#EEE")';
        $header = $options['header'] ?? 'var(--primary, "#EEE")';

        if ($theme != 'light' && $background == '#FFF') {
            $background = $options['background-color'] ?? '#111';
        }

        if ($theme != 'light' && $color == '#FFF') {
            $color = $options['color'] ?? '#FFF';
        }

        if ($theme != 'light' && $numberColor == '#111') {
            $numberColor = $options['number-color'] ?? '#FFF';
        }

        return <<<EOT
.qs-datepicker-container {
    color: ${numberColor};
    background-color: ${background};
}
.qs-datepicker .qs-controls {
    background-color: ${header};
    color: ${color};
    height: 35px;
}
.qs-datepicker .qs-square {
    height: 32px;
}
.qs-datepicker .qs-square.qs-active {
    background-color: ${highlight};
    color: ${color};
}
.qs-datepicker .qs-square:not(.qs-empty):not(.qs-disabled):not(.qs-day):not(.qs-active):hover {
    background-color: ${highlight};
    color: ${color};
}
.qs-datepicker .qs-arrow.qs-left:after {
    border-right-color: ${color};
}
.qs-datepicker .qs-arrow.qs-right:after {
    border-left-color: ${color};
}
EOT;
    }

    protected static function js($id, $options)
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
