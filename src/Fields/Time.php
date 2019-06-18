<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Time extends Field
{
    protected static function getType()
    {
        return 'time';
    }
}