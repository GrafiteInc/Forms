<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Telephone extends Field
{
    protected static function getType()
    {
        return 'tel';
    }

    protected static function getFactory()
    {
        return 'phone';
    }
}
