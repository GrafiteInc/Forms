<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Name extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getFactory()
    {
        return 'name';
    }
}
