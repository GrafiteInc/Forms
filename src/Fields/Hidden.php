<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

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
