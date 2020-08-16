<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

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
