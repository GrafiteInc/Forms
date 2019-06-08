<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class CheckboxInline extends Field
{
    protected static function getType()
    {
        return 'checkbox-inline';
    }

    protected static function getOptions()
    {
        return [
            'class' => 'form-check-input',
        ];
    }
}
