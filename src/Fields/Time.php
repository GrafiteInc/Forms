<?php

namespace Grafite\Forms\Fields;

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
