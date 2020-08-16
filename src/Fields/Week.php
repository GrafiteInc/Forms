<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Week extends Field
{
    protected static function getType()
    {
        return 'week';
    }

    protected static function getFactory()
    {
        return 'week';
    }
}
