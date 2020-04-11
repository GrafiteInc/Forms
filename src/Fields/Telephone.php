<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Telephone extends Field
{
    protected static function getType()
    {
        return 'tel';
    }

    protected static function getFactory()
    {
        return 'phone';
    }
}
