<?php

namespace Grafite\FormMaker\Fields;

use Grafite\FormMaker\Fields\Field;

class HasOne extends Field
{
    protected static function getType()
    {
        return 'relationship';
    }
}
