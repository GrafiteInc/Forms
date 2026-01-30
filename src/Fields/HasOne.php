<?php

namespace Grafite\Forms\Fields;

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
