<?php

namespace Grafite\Forms\Fields\Bootstrap;

class Suggest extends TomSelect
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getAttributes()
    {
        return [];
    }
}
