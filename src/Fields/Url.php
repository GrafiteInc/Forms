<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

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
