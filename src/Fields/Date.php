<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Date extends Field
{
    protected static function getType()
    {
        return 'date';
    }

    protected static function getOptions()
    {
        return [
            'format' => 'Y-m-d',
            'before' => 'Date',
        ];
    }
}