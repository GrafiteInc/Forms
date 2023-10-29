<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class File extends Field
{
    protected static function getType()
    {
        return 'file';
    }

    protected static function getOptions()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'image';
    }
}
