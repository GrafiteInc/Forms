<?php

namespace Grafite\Forms\Fields;

class Week extends Field
{
    protected static function getType()
    {
        return 'week';
    }

    protected static function getFactory()
    {
        return 'week';
    }
}
