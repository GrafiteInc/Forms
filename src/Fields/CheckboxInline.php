<?php

namespace Grafite\Forms\Fields;

class CheckboxInline extends Field
{
    protected static function getType()
    {
        return 'checkbox-inline';
    }

    protected static function getOptions()
    {
        return [
            'class' => '',
        ];
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
