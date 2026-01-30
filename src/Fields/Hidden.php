<?php

namespace Grafite\Forms\Fields;

class Hidden extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }
}
