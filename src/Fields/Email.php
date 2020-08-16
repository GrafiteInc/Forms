<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

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
