<?php

/*
|--------------------------------------------------------------------------
| Form Maker Config
|--------------------------------------------------------------------------
*/

return [

    'form' => [
        'group-class' => 'form-group',
        'error-class' => 'has-error',
        'label-class' => 'control-label',
        'before_after_input_wrapper' => 'input-group',

        /*
         * --------------------------------------------------------------------------
         * For Horizontal forms
         * --------------------------------------------------------------------------
         *  You may wish to use horizontal forms. If you do, you need to set the
         *  orientation to horizontal, and ensure that your form has the 'form-horizontal'
         *  class.
        */

        'orientation' => 'vertical',
        'label-column' => 'col-md-2',
        'input-column' => 'col-md-10',
        'checkbox-column' => 'col-md-offset-2 col-md-10',
    ],

    'inputTypes' => [
        'number'            => 'number',
        'integer'           => 'number',
        'float'             => 'number',
        'decimal'           => 'number',
        'boolean'           => 'number',
        'string'            => 'text',
        'email'             => 'text',
        'varchar'           => 'text',
        'file'              => 'file',
        'image'             => 'file',
        'datetime'          => 'date',
        'date'              => 'date',
        'password'          => 'password',
        'textarea'          => 'textarea',
        'select'            => null,
        'checkbox'          => null,
        'checkbox-inline'   => null,
        'radio'             => null,
        'radio-inline'      => null,
    ]
];
