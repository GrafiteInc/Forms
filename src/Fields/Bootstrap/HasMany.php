<?php

namespace Grafite\Forms\Fields\Bootstrap;

class HasMany extends TomSelect
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
}
