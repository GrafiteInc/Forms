<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

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
