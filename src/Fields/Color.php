<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

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
        ];
    }

    protected static function getFactory()
    {
        return 'safeColorName';
    }
}
