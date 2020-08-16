<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Number extends Field
{
    protected static function getType()
    {
        return 'number';
    }

    /**
     * Get factory
     *
     * @return string
     */
    protected static function getFactory()
    {
        return 'randomNumber()';
    }
}
