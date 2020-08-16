<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

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
