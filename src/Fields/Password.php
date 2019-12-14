<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Password extends Field
{
    protected static function getType()
    {
        return 'password';
    }

    protected static function getFactory()
    {
        return 'password';
    }
}