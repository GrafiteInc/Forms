<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Bootstrap\Select;

class Day extends Select
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getOptions()
    {
        return [
            'options' => [
                'Sunday' => 'sunday',
                'Monday' => 'monday',
                'Tuesday' => 'tuesday',
                'Wednesday' => 'wednesday',
                'Thursday' => 'thursday',
                'Friday' => 'friday',
                'Saturday' => 'saturday',
            ],
        ];
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker w-100 form-control',
            'data-live-search' => 'true',
            'multiple' => false,
            'null_label' => 'Please select a day',
            'null_value' => null,
            'data-size' => 8,
        ];
    }
}
