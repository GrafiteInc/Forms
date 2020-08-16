<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

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
