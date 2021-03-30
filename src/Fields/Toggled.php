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
        let {$id}_checkbox = document.getElementById('{$id}').parentNode;
        let {$id}_toggle = document.createElement("span");
            {$id}_toggle.classList.add('{$id}_slider');
            {$id}_toggle.classList.add('slider');
            {$id}_toggle.classList.add('round');
        {$id}_checkbox.appendChild({$id}_toggle);
        {$id}_checkbox.classList.add('d-inline-block');

        {$id}_checkbox.addEventListener('click', () => {
            {$id}_checkbox.querySelector('.form-check-input').click()
        });
EOT;
    }

    protected static function styles($id, $options)
    {
        $color = $options['color'] ?? 'var(--primary)';

        return <<<EOT
            .form-check .${id}_slider {
                position: absolute;
                display: inline-block;
                width: 60px;
                height: 34px;
            }

            .form-check-label[for="{$id}"]:not(:empty) {
                margin-left: 70px;
                line-height: 34px;
            }

            .form-check #${id} {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .${id}_slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
            }

            .${id}_slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .2s;
            }

            #${id}:checked + label + .${id}_slider{
                background-color: {$color};
            }

            #${id}:focus + label + .${id}_slider{
                box-shadow: 0 0 1px {$color};
            }

             #${id}:checked + label + .${id}_slider:before{
                transform: translateX(26px);
            }

            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }
EOT;
    }
}
