<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Bootstrap\Select;

class Suggest extends Select
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker',
            'data-live-search' => "true",
            'multiple' => true,
            'data-size' => 8,
        ];
    }
}
