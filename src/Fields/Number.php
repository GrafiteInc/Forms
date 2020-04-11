<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

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
