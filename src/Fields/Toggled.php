<?php

namespace Grafite\Forms\Fields;

use Illuminate\Support\Str;
use Grafite\Forms\Fields\Field;

class Toggled extends Field
{
    protected static function getType()
    {
        return 'checkbox';
    }

    protected static function getOptions()
    {
        return [
            'label' => false,
            'class' => ''
        ];
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'boolean';
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_toggledField';
    }

    public static function js($id, $options)
    {
        return <<<JS
        window._formsjs_toggledField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _checkbox = element.parentNode;
                    _checkbox.classList.add('toggle-slider-wrapper');
                let _toggle = document.createElement("span");
                    _toggle.classList.add('toggle_slider');
                    _toggle.classList.add('slider');
                    _toggle.classList.add('round');
                _checkbox.appendChild(_toggle);
                _checkbox.classList.add('d-inline-block');

                _checkbox.addEventListener('click', () => {
                    _checkbox.querySelector('.form-check-input').click()
                });
            }
        }
JS;
    }

    public static function styles($id, $options)
    {
        $colorVariable = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? '--bs-primary' : '--primary';
        $position = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? 'relative' : 'absolute';
        $labelSpacing = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? ".form-check-label[for=\"{$id}\"] {margin-left: -24px;}" : '';

        $color = $options['color'] ?? "var($colorVariable)";

        return <<<CSS
            .form-check .toggle_slider {
                position: {$position};
                display: inline-block;
                width: 60px;
                height: 34px;
            }

            {$labelSpacing}

            .toggle-slider-wrapper .form-check-label:not(:empty) {
                line-height: 34px;
                vertical-align: top;
                margin-right: 24px;
            }

            .toggle-slider-wrapper .form-check-input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .toggle_slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
            }

            .toggle_slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .2s;
            }

            .form-check-input:checked + label + .toggle_slider {
                background-color: {$color};
            }

            .form-check-input:focus + label + .toggle_slider{
                box-shadow: 0 0 1px {$color};
            }

             .form-check-input:checked + label + .toggle_slider:before{
                transform: translateX(26px);
            }

            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }
CSS;
    }
}
