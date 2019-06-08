<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Hidden extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }
}
