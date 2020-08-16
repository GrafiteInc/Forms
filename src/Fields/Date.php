<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Date extends Field
{
    protected static function getType()
    {
        return 'date';
    }

    protected static function getOptions()
    {
        return [
            'format' => 'Y-m-d',
            'before' => 'Date',
        ];
    }

    protected static function getFactory()
    {
        return 'date';
    }
}
