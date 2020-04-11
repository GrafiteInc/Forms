<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Url extends Field
{
    protected static function getType()
    {
        return 'url';
    }

    protected static function getFactory()
    {
        return 'url';
    }
}
