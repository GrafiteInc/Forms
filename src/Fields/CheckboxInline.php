<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

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
