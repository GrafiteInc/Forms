<?php

namespace Grafite\Forms\Fields;

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
