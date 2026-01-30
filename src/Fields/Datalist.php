<?php

namespace Grafite\Forms\Fields;

class Datalist extends Field
{
    protected static function getType()
    {
        return 'datalist';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }
}
