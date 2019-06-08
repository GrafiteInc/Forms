<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Email extends Field
{
    protected static function getType()
    {
        return 'email';
    }
}
