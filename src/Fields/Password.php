<?php

namespace Grafite\Forms\Fields;

class Password extends Field
{
    protected static function getType()
    {
        return 'password';
    }

    protected static function getFactory()
    {
        return 'password';
    }
}
