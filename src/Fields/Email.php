<?php

namespace Grafite\Forms\Fields;

class Email extends Field
{
    protected static function getType()
    {
        return 'email';
    }

    protected static function getFactory()
    {
        return 'email';
    }
}
