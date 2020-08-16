<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

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
