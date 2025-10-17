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
        $options = [];

        foreach (timezone_identifiers_list() as $timezone) {
            $options[$timezone] = $timezone;
        }

        return [
            'options' => $options,
        ];
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'form-select selectpicker',
            'data-live-search' => 'true',
            'multiple' => false,
            'data-size' => 8,
        ];
    }
}
