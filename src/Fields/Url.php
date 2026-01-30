<?php

namespace Grafite\Forms\Fields;

class Url extends Field
{
    protected static function getType()
    {
        return 'url';
    }

    protected static function getFactory()
    {
        return 'url';
    }
}
