<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Time extends Field
{
    protected static function getType()
    {
        return 'time';
    }

    protected static function getFactory()
    {
        return 'time';
    }
}
