<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class HasMany extends Field
{
    protected static function getType()
    {
        return 'relationship';
    }

    protected static function getAttributes()
    {
        return [
            'multiple' => true,
        ];
    }

    protected static function getFactory()
    {
        return null;
    }
}
