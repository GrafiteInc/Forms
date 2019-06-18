<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Textarea extends Field
{
    protected static function getType()
    {
        return 'textarea';
    }

    protected static function getAttributes()
    {
        return [
            'rows' => 5,
        ];
    }
}
