<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class HasOne extends Field
{
    protected static function getType()
    {
        return 'relationship';
    }

    protected static function getFactory()
    {
        return null;
    }
}
