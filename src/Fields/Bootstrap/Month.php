<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Bootstrap\Select;

class Month extends Select
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getOptions()
    {
        return [
            'options' => [
                'January' => 1,
                'February' => 2,
                'March' => 3,
                'April' => 4,
                'May' => 5,
                'June' => 6,
                'July' => 7,
                'August' => 8,
                'September' => 9,
                'October' => 10,
                'November' => 11,
                'December' => 12,
            ],
        ];
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker w-100 form-control',
            'data-live-search' => 'true',
            'multiple' => false,
            'null_label' => 'Please select a month',
            'null_value' => null,
            'data-size' => 8,
        ];
    }
}
