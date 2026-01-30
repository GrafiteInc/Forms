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
        ];
    }

    protected static function getFactory()
    {
        return 'safeColorName';
    }
}
