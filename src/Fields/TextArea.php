<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class TextArea extends Field
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

    protected static function getFactory()
    {
        return 'text(300)';
    }
}
