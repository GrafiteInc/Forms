<?php

namespace Grafite\Forms\Fields;

class Color extends Field
{
    protected static function getType()
    {
        return 'color';
    }

    protected static function getOptions()
    {
        return [
            'before' => 'Color',
            'class' => 'form-control forms-color-selector',
        ];
    }

    protected static function getFactory()
    {
        return 'safeColorName';
    }

    public static function js($id, $options)
    {
        return <<<'JS'
            document.addEventListener("DOMContentLoaded", function () {
                let colorInputs = document.querySelectorAll('.forms-color-selector');
                colorInputs.forEach(function (input) {
                    input.style.height = input.parentNode.offsetHeight + 'px';
                });
            });
JS;
    }
}
