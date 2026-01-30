<?php

namespace Grafite\Forms\Fields;

class Text extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }
}
