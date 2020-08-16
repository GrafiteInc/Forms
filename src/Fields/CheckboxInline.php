<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class CheckboxInline extends Field
{
    protected static function getType()
    {
        return 'checkbox-inline';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'form-check-input',
        ];
    }

    protected static function getFactory()
    {
        return 'boolean';
    }
}
