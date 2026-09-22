<?php

namespace Grafite\Forms\Fields;

class Pin extends Field
{
    protected static function fieldOptions()
    {
        return [
            'length',
            'mode',
        ];
    }

    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getFactory()
    {
        return "numerify('######')";
    }

    /**
     * Resolve the length of the pin, clamped to something renderable
     *
     * @param  array  $options
     * @return int
     */
    protected static function getLength($options)
    {
        return max(1, min(12, (int) ($options['length'] ?? 6)));
    }

    /**
     * Resolve the inputmode, pattern and strip regex for the mode
     *
     * @param  array  $options
     * @return array
     */
    protected static function getMode($options)
    {
        return match ($options['mode'] ?? 'numeric') {
            'alphanumeric' => [
                'inputmode' => 'text',
                'pattern' => '[A-Za-z0-9]*',
                'strip' => '[^A-Za-z0-9]',
            ],
            'alpha' => [
                'inputmode' => 'text',
                'pattern' => '[A-Za-z]*',
                'strip' => '[^A-Za-z]',
            ],
            default => [
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
                'strip' => '[^0-9]',
            ],
        };
    }

    public static function getTemplate($options)
    {
        $length = static::getLength($options);
        $mode = static::getMode($options);

        $inputs = [];

        for ($i = 0; $i < $length; $i++) {
            $inputs[] = '<input type="text" id="Pin_{id}_'.$i.'" class="forms-pin-input" data-forms-pin-index="'.$i.'" maxlength="1" inputmode="'.$mode['inputmode'].'" pattern="'.$mode['pattern'].'" autocomplete="one-time-code" autocapitalize="off" autocorrect="off" spellcheck="false" aria-label="Character '.($i + 1).' of '.$length.'">';
        }

        $inputs = implode("\n            ", $inputs);

        return <<<HTML
<div class="{rowClass}">
    <label for="Pin_{id}_0" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        {field}
        <div class="forms-pin" id="Pin_{id}" role="group" aria-label="{name}">
            {$inputs}
        </div>
        {errors}
    </div>
</div>
HTML;
    }

    public static function styles($id, $options)
    {
        return <<<'CSS'
.forms-pin {
    --forms-pin-size: 3rem;
    --forms-pin-gap: 0.5rem;
    --forms-pin-color: var(--bs-body-color, #212529);
    --forms-pin-bg: var(--bs-body-bg, #ffffff);
    --forms-pin-border-color: var(--bs-border-color, #ced4da);
    --forms-pin-focus-color: var(--bs-primary, #0d6efd);
    --forms-pin-invalid-color: var(--bs-danger, #dc3545);
    --forms-pin-disabled-bg: var(--bs-secondary-bg, #e9ecef);
    display: flex;
    flex-wrap: wrap;
    gap: var(--forms-pin-gap);
}

.forms-pin-input {
    width: var(--forms-pin-size);
    height: var(--forms-pin-size);
    padding: 0;
    color: var(--forms-pin-color);
    background-color: var(--forms-pin-bg);
    border: 1px solid var(--forms-pin-border-color);
    border-radius: var(--bs-border-radius, 0.375rem);
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    box-sizing: border-box;
    appearance: none;
}

.forms-pin-input:focus {
    outline: 0;
    border-color: var(--forms-pin-focus-color);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.forms-pin-input:disabled,
.forms-pin-input[readonly] {
    background-color: var(--forms-pin-disabled-bg);
    opacity: 1;
}

.forms-pin-input.is-invalid,
.is-invalid ~ .forms-pin .forms-pin-input {
    border-color: var(--forms-pin-invalid-color);
}

@media (prefers-color-scheme: dark) {
    .forms-pin {
        --forms-pin-color: var(--bs-body-color, #dee2e6);
        --forms-pin-bg: var(--bs-body-bg, #212529);
        --forms-pin-border-color: var(--bs-border-color, #495057);
        --forms-pin-disabled-bg: var(--bs-secondary-bg, #343a40);
    }
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_pinField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'length' => static::getLength($options),
            'strip' => static::getMode($options)['strip'],
        ]);
    }

    public static function js($id, $options)
    {
        return <<<'JS'
        window._formsjs_pinField = function (element) {
            if (element.getAttribute('data-formsjs-rendered')) {
                return;
            }

            let _id = element.getAttribute('id');
            let _wrapper = document.getElementById('Pin_' + _id);

            if (! _wrapper) {
                return;
            }

            let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data') || '{}');
            let _boxes = Array.prototype.slice.call(_wrapper.querySelectorAll('.forms-pin-input'));

            if (! _boxes.length) {
                return;
            }

            let _strip = new RegExp(_config.strip || '[^0-9]', 'g');

            let _sanitize = function (value) {
                return String(value == null ? '' : value).replace(_strip, '');
            };

            let _sync = function () {
                element.value = _boxes.map(function (_box) {
                    return _box.value;
                }).join('');

                element.dispatchEvent(new Event('change', { bubbles: true }));
            };

            let _focusNextEmpty = function (_from) {
                for (let _i = _from; _i < _boxes.length; _i++) {
                    if (! _boxes[_i].value) {
                        _boxes[_i].focus();
                        return;
                    }
                }

                let _last = _boxes[_boxes.length - 1];
                _last.focus();
                _last.select();
            };

            let _fill = function (_text, _start) {
                let _characters = _sanitize(_text).split('');

                if (! _characters.length) {
                    return;
                }

                for (let _i = _start; _i < _boxes.length && _characters.length; _i++) {
                    _boxes[_i].value = _characters.shift();
                }

                _focusNextEmpty(_start);
            };

            _boxes.forEach(function (_box, _index) {
                _box.addEventListener('input', function () {
                    let _value = _sanitize(_box.value);

                    if (_value.length > 1) {
                        _box.value = '';
                        _fill(_value, _index);
                    } else {
                        _box.value = _value;

                        if (_value && _index < _boxes.length - 1) {
                            _boxes[_index + 1].focus();
                            _boxes[_index + 1].select();
                        }
                    }

                    _sync();
                });

                _box.addEventListener('paste', function (event) {
                    event.preventDefault();

                    let _clipboard = (event.clipboardData || window.clipboardData);

                    _fill(_clipboard ? _clipboard.getData('text') : '', _index);
                    _sync();
                });

                _box.addEventListener('keydown', function (event) {
                    if (event.key === 'Backspace') {
                        if (_box.value) {
                            return;
                        }

                        event.preventDefault();

                        if (_index > 0) {
                            _boxes[_index - 1].value = '';
                            _boxes[_index - 1].focus();
                            _sync();
                        }
                    } else if (event.key === 'Delete') {
                        event.preventDefault();
                        _box.value = '';
                        _sync();
                    } else if (event.key === 'ArrowLeft' && _index > 0) {
                        event.preventDefault();
                        _boxes[_index - 1].focus();
                        _boxes[_index - 1].select();
                    } else if (event.key === 'ArrowRight' && _index < _boxes.length - 1) {
                        event.preventDefault();
                        _boxes[_index + 1].focus();
                        _boxes[_index + 1].select();
                    }
                });

                _box.addEventListener('focus', function () {
                    _box.select();
                });

                if (element.disabled) {
                    _box.setAttribute('disabled', 'disabled');
                }

                if (element.readOnly) {
                    _box.setAttribute('readonly', 'readonly');
                }

                if (element.required) {
                    _box.setAttribute('required', 'required');
                }
            });

            if (element.value) {
                _fill(element.value, 0);
                element.value = _boxes.map(function (_box) {
                    return _box.value;
                }).join('');
            }
        }
JS;
    }
}
