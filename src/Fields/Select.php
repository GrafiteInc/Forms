<?php

namespace Grafite\Forms\Fields;

class Select extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }
}
