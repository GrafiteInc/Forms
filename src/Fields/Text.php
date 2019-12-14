<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

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
