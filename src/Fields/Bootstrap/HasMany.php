<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Bootstrap\Select;

class HasMany extends Select
{
    protected static function getType()
    {
        return 'relationship';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker',
            'multiple' => true,
            'data-size' => 8,
        ];
    }
}
