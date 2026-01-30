<?php

namespace Grafite\Forms\Fields;

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
