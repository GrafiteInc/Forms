<?php

namespace Grafite\Forms\Fields;

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
