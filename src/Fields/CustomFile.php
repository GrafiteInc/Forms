<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class CustomFile extends Field
{
    protected static function getType()
    {
        return 'custom-file';
    }

    protected static function getOptions()
    {
        return [
            'before' => 'Upload',
        ];
    }

    protected static function getFactory()
    {
        return 'image';
    }
}