<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Image extends Field
{
    protected static function getType()
    {
        return 'image';
    }

    protected static function getOptions()
    {
        return [
            'before' => 'Image',
        ];
    }
}