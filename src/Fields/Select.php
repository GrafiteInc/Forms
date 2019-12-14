<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Select extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }
}
