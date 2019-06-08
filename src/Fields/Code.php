<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Code extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getAttributes()
    {
        return [
            'rows' => 5,
        ];
    }
}
