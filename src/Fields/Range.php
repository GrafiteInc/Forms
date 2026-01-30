<?php

namespace Grafite\Forms\Fields;

class Range extends Field
{
    protected static function getType()
    {
        return 'range';
    }

    protected static function getFactory()
    {
        return 'numberBetween(1, 10)';
    }
}
