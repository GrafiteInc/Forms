<?php

namespace Grafite\Forms\Fields;

class DualRange extends Field
{
    protected static function fieldOptions()
    {
        return [
            'min',
            'max',
            'step',
            'lower_value',
            'upper_value',
            'prefix',
            'suffix',
            'show_values',
            'precision',
        ];
    }

    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getFactory()
    {
        return "numberBetween(1, 10).','.\$faker->numberBetween(11, 20)";
    }

    public static function getTemplate($options)
    {
        $min = $options['min'] ?? 0;
        $max = $options['max'] ?? 100;
        $step = $options['step'] ?? 1;
        $lower = $options['lower_value'] ?? $min;
        $upper = $options['upper_value'] ?? $max;
        $showValues = ($options['show_values'] ?? true) ? '' : ' style="display: none;"';

        return <<<HTML
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        {field}
        <div class="dual-range-input forms-dual-range" id="DualRange_{id}">
            <input type="range" min="{$min}" max="{$max}" step="{$step}" value="{$lower}" aria-label="Minimum value">
            <input type="range" min="{$min}" max="{$max}" step="{$step}" value="{$upper}" aria-label="Maximum value">
        </div>
        <div class="forms-dual-range-values d-flex justify-content-between small text-muted mt-1"{$showValues}>
            <span id="DualRange_{id}_LowerLabel"></span>
            <span id="DualRange_{id}_UpperLabel"></span>
        </div>
        {errors}
    </div>
</div>
HTML;
    }

    public static function styles($id, $options)
    {
        return <<<'CSS'
.dual-range-input {
    --dri-height: 1.5rem;
    --dri-thumb-width: 1.25rem;
    --dri-thumb-height: 1.25rem;
    --dri-thumb-color: var(--bs-body-bg, #ffffff);
    --dri-thumb-hover-color: var(--bs-primary-bg-subtle, #a8d5ff);
    --dri-thumb-active-color: var(--bs-primary, #0d6efd);
    --dri-thumb-border-color: var(--bs-border-color, #ced4da);
    --dri-thumb-border-hover-color: var(--dri-thumb-border-color);
    --dri-thumb-border-active-color: var(--dri-thumb-border-color);
    --dri-thumb-border-radius: 1rem;
    --dri-thumb-border-width: 1px;
    --dri-track-height: 0.25rem;
    --dri-track-border-radius: 1rem;
    --dri-track-color: var(--bs-secondary-bg, #e9ecef);
    --dri-track-filled-color: var(--bs-primary, #0d6efd);
    --dri-track-filled-gradient-mid-color: var(--dri-track-filled-color);
    --dri-track-filled-gradient-end-color: var(--dri-track-filled-color);
    display: flex;
    height: var(--dri-height);
    max-width: 100%;
    width: 100%;
    box-sizing: border-box;
    padding-inline-end: calc(var(--dri-thumb-width) * 2);
}

.dual-range-input:has(input:focus-visible) {
    outline: 2px solid var(--dri-thumb-active-color);
    outline-offset: 4px;
    border-radius: 2px;
}

.dual-range-input input {
    -webkit-tap-highlight-color: transparent;
    -webkit-appearance: none;
    appearance: none;
    background: none;
    border-radius: 0;
    flex-basis: calc(50% + var(--dri-thumb-width));
    flex-shrink: 0;
    font-size: inherit;
    height: 100%;
    margin: 0;
    min-width: var(--dri-thumb-width);
    outline: none;
    padding: 0;
}

.dual-range-input input::-moz-range-track {
    background-color: var(--dri-track-color);
    background-repeat: no-repeat;
    box-sizing: border-box;
    height: var(--dri-track-height);
}

.dual-range-input input:first-child::-moz-range-track {
    border-start-start-radius: var(--dri-track-border-radius);
    border-end-start-radius: var(--dri-track-border-radius);
    background-image: linear-gradient(to right, var(--dri-track-color) var(--dri-gradient-position), var(--dri-track-filled-color) var(--dri-gradient-position), var(--dri-track-filled-gradient-mid-color));
}

.dual-range-input input:last-child::-moz-range-track {
    border-start-end-radius: var(--dri-track-border-radius);
    border-end-end-radius: var(--dri-track-border-radius);
    background-image: linear-gradient(to right, var(--dri-track-filled-gradient-mid-color), var(--dri-track-filled-gradient-end-color) var(--dri-gradient-position), var(--dri-track-color) var(--dri-gradient-position));
}

.dual-range-input input::-moz-range-thumb {
    -webkit-appearance: none;
    appearance: none;
    background-color: var(--dri-thumb-color);
    border-radius: var(--dri-thumb-border-radius);
    border: var(--dri-thumb-border-width) solid var(--dri-thumb-border-color);
    box-shadow: none;
    box-sizing: border-box;
    height: var(--dri-thumb-height);
    width: var(--dri-thumb-width);
    max-width: 99.99%;
}

.dual-range-input input:not([data-ready=true])::-moz-range-thumb {
    opacity: 0;
}

.dual-range-input input:hover::-moz-range-thumb {
    background-color: var(--dri-thumb-hover-color);
    border-color: var(--dri-thumb-border-hover-color);
}

.dual-range-input input:active::-moz-range-thumb,
.dual-range-input input:focus-visible::-moz-range-thumb {
    background-color: var(--dri-thumb-active-color);
    border-color: var(--dri-thumb-border-active-color);
}

.dual-range-input input::-webkit-slider-runnable-track {
    background-color: var(--dri-track-color);
    background-repeat: no-repeat;
    box-sizing: border-box;
    height: var(--dri-track-height);
}

.dual-range-input input:first-child::-webkit-slider-runnable-track {
    border-start-start-radius: var(--dri-track-border-radius);
    border-end-start-radius: var(--dri-track-border-radius);
    background-image: linear-gradient(to right, var(--dri-track-color) var(--dri-gradient-position), var(--dri-track-filled-color) var(--dri-gradient-position), var(--dri-track-filled-gradient-mid-color));
}

.dual-range-input input:last-child::-webkit-slider-runnable-track {
    border-start-end-radius: var(--dri-track-border-radius);
    border-end-end-radius: var(--dri-track-border-radius);
    background-image: linear-gradient(to right, var(--dri-track-filled-gradient-mid-color), var(--dri-track-filled-gradient-end-color) var(--dri-gradient-position), var(--dri-track-color) var(--dri-gradient-position));
}

.dual-range-input input::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    background-color: var(--dri-thumb-color);
    border-radius: var(--dri-thumb-border-radius);
    border: var(--dri-thumb-border-width) solid var(--dri-thumb-border-color);
    box-shadow: none;
    box-sizing: border-box;
    height: var(--dri-thumb-height);
    width: var(--dri-thumb-width);
    margin-top: calc(var(--dri-track-height) / 2);
    transform: translateY(-50%);
}

.dual-range-input input:not([data-ready=true])::-webkit-slider-thumb {
    opacity: 0;
}

.dual-range-input input:hover::-webkit-slider-thumb {
    background-color: var(--dri-thumb-hover-color);
    border-color: var(--dri-thumb-border-hover-color);
}

.dual-range-input input:active::-webkit-slider-thumb,
.dual-range-input input:focus-visible::-webkit-slider-thumb {
    background-color: var(--dri-thumb-active-color);
    border-color: var(--dri-thumb-border-active-color);
}

@media (prefers-color-scheme: dark) {
    .dual-range-input {
        --dri-thumb-color: var(--bs-body-bg, #212529);
        --dri-thumb-border-color: var(--bs-border-color, #495057);
        --dri-track-color: var(--bs-secondary-bg, #343a40);
    }
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_dualRangeField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'prefix' => $options['prefix'] ?? '',
            'suffix' => $options['suffix'] ?? '',
            'precision' => $options['precision'] ?? 3,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<'JS'
        window._formsjs_dualRangeField = function (element) {
            if (element.getAttribute('data-formsjs-rendered')) {
                return;
            }

            let _id = element.getAttribute('id');
            let _wrapper = document.getElementById('DualRange_' + _id);

            if (! _wrapper) {
                return;
            }

            let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data') || '{}');
            let _min = _wrapper.querySelector('input[type=range]:first-child');
            let _max = _wrapper.querySelector('input[type=range]:last-child');

            if (! _min || ! _max) {
                return;
            }

            let _precision = _config.precision || 3;
            let _thumbWidthVar = 'var(--dri-thumb-width)';
            let _lowerLabel = document.getElementById('DualRange_' + _id + '_LowerLabel');
            let _upperLabel = document.getElementById('DualRange_' + _id + '_UpperLabel');

            if (element.value && element.value.indexOf(',') !== -1) {
                let _parts = element.value.split(',');
                _min.value = _parts[0];
                _max.value = _parts[1];
            }

            let _format = function (value) {
                return (_config.prefix || '') + value + (_config.suffix || '');
            };

            let _update = function (method) {
                method = method || 'ceil';

                let _lowerBound = parseFloat(_min.min);
                let _upperBound = parseFloat(_max.max);
                let _step = parseFloat(_min.step) || 1;
                let _minValue = parseFloat(_min.value);
                let _maxValue = parseFloat(_max.value);
                let _midValue = (_maxValue - _minValue) / 2;
                let _mid = _minValue + Math[method](_midValue / _step) * _step;
                let _range = _upperBound - _lowerBound;
                let _leftWidth = (((_mid - _lowerBound) / _range) * 100).toFixed(_precision);
                let _rightWidth = (((_upperBound - _mid) / _range) * 100).toFixed(_precision);

                _min.style.flexBasis = 'calc(' + _leftWidth + '% + ' + _thumbWidthVar + ')';
                _max.style.flexBasis = 'calc(' + _rightWidth + '% + ' + _thumbWidthVar + ')';

                _min.max = _mid.toFixed(_precision);
                _max.min = _mid.toFixed(_precision);

                let _minFill = (_minValue - _lowerBound) / (_mid - _lowerBound) || 0;
                let _maxFill = (_maxValue - _mid) / (_upperBound - _mid) || 0;
                let _minFillPercentage = (_minFill * 100).toFixed(_precision);
                let _maxFillPercentage = (_maxFill * 100).toFixed(_precision);
                let _minFillThumb = (0.5 - _minFill).toFixed(_precision);
                let _maxFillThumb = (0.5 - _maxFill).toFixed(_precision);

                _min.style.setProperty('--dri-gradient-position', 'calc(' + _minFillPercentage + '% + (' + _minFillThumb + ' * ' + _thumbWidthVar + '))');
                _max.style.setProperty('--dri-gradient-position', 'calc(' + _maxFillPercentage + '% + (' + _maxFillThumb + ' * ' + _thumbWidthVar + '))');
            };

            let _syncLabels = function () {
                if (_lowerLabel) {
                    _lowerLabel.textContent = _format(_min.value);
                }
                if (_upperLabel) {
                    _upperLabel.textContent = _format(_max.value);
                }
            };

            let _syncValue = function () {
                element.value = _min.value + ',' + _max.value;
                element.dispatchEvent(new Event('change', { bubbles: true }));
                _syncLabels();
            };

            _min.addEventListener('input', function () { _update('ceil'); _syncValue(); });
            _max.addEventListener('input', function () { _update('floor'); _syncValue(); });
            _min.addEventListener('focus', function () { _update('ceil'); });
            _max.addEventListener('focus', function () { _update('floor'); });
            _min.addEventListener('mousedown', function () { _update('ceil'); });
            _max.addEventListener('mousedown', function () { _update('floor'); });
            _min.addEventListener('touchstart', function () { _update('ceil'); });
            _max.addEventListener('touchstart', function () { _update('floor'); });

            if (element.disabled || element.readOnly) {
                _min.setAttribute('disabled', 'disabled');
                _max.setAttribute('disabled', 'disabled');
            }

            _update();
            _min.dataset.ready = 'true';
            _max.dataset.ready = 'true';

            if (! element.value) {
                element.value = _min.value + ',' + _max.value;
            }

            _syncLabels();
        }
JS;
    }
}
