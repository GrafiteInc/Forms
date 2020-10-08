<?php

namespace Grafite\Forms\Fields;

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

    protected static function js($id, $options)
    {
        return <<<EOT
            [...document.getElementsByClassName('form-check')].forEach((item) => {
                let node = document.createElement("span");
                    node.classList.add('slider');
                    node.classList.add('round');
                item.appendChild(node);

                item.addEventListener('click', () => {
                    item.querySelector('.form-check-input').click()
                });
            });
EOT;
    }

    protected static function styles($id, $options)
    {
        $color = $options['color'] ?? 'blue';

        return <<<EOT
            .form-check {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 34px;
            }

            .form-check input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                -webkit-transition: .4s;
                transition: .4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            input:checked + label + .slider {
                background-color: {$color};
            }

            input:focus + label + .slider {
                box-shadow: 0 0 1px {$color};
            }

            input:checked + label + .slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }

            /* Rounded sliders */
            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }
EOT;
    }
}
