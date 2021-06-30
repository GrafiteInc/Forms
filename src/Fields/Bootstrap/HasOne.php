<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Bootstrap\Select;

class HasOne extends Select
{
    protected static function getType()
    {
        return 'relationship';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker',
            'data-size' => 8,
        ];
    }
}
