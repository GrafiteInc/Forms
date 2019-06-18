<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Month extends Field
{
    protected static function getType()
    {
        return 'month';
    }
}