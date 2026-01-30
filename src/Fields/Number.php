<?php

namespace Grafite\Forms\Fields;

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
