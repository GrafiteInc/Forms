<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Radio extends Field
{
    protected static function getType()
    {
        return 'radio';
    }

    protected static function getOptions()
    {
        return [
            'class' => 'form-check-input'
        ];
    }
}
