<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Checkbox extends Field
{
    protected static function getType()
    {
        return 'checkbox';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'form-check-input'
        ];
    }
}
