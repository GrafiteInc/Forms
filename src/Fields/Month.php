<?php

namespace Grafite\Forms\Fields;

class Month extends Field
{
    protected static function getType()
    {
        return 'month';
    }

    protected static function getFactory()
    {
        return 'month';
    }
}
