<?php

namespace Grafite\Forms\Fields;

class Search extends Field
{
    protected static function getType()
    {
        return 'search';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }
}
