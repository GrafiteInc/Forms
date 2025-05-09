<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Bootstrap\TomSelect;

class Timezone extends TomSelect
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getOptions()
    {
        return [
            'options' => timezone_identifiers_list(),
        ];
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker w-100 form-control',
            'data-live-search' => 'true',
            'multiple' => false,
            'data-size' => 8,
        ];
    }
}
