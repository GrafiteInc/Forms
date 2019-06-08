<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class Text extends Field
{
    protected static function getType()
    {
        return 'string';
    }
}
