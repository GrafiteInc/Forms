<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Bootstrap\TomSelect;

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
