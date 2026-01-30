<?php

namespace Grafite\Forms\Fields;

class SwitchToggle extends Field
{
    protected static function getType()
    {
        return 'switch';
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
