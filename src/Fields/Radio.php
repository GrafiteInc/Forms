<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Radio extends Field
{
    protected static function getType()
    {
        return 'radio';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'form-check-input'
        ];
    }
}
