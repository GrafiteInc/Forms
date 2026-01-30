<?php

namespace Grafite\Forms\Fields\Bootstrap;

class Day extends TomSelect
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
            'class' => 'w-100 form-control',
            'multiple' => false,
            'null_label' => 'Please select a day',
            'null_value' => null,
            'data-size' => 8,
        ];
    }
}
