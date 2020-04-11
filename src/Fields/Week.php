<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

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
