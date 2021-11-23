<?php

/*
|--------------------------------------------------------------------------
| Forms Config
|--------------------------------------------------------------------------
*/

return [

    'bootstrap-version' => '5.0',

    'modal-centered' => true,

    'buttons' => [
        'submit' => 'btn btn-primary',
        'edit' => 'btn btn-outline-primary',
        'delete' => 'btn btn-danger',
        'cancel' => 'btn btn-secondary',
    ],

    'html' => [
        'pagination' => 'd-flex justify-content-center mt-4 mb-0',
        'table' => 'table table-borderless m-0 p-0',
        'table-head' => 'thead border-bottom',
        'table-actions-header' => '<th class="text-end">Actions</th>',
        'sortable-icon' => '<span class="fas fa-fw fa-arrows-alt-v"></span>',
        'list-group' => 'list-group list-group-flush',
        'list-group-item' => 'list-group-item',
        'badge-tag' => 'badge bg-primary float-end mt-1',
    ],

    'form' => [
        'class' => 'form',
        'delete-class' => 'form-inline',
        'inline-class' => 'form d-inline',

        'group-class' => 'form-group',
        'input-class' => 'form-control',
        'label-class' => 'form-label',
        'label-check-class' => 'form-check-label',
        'before-after-input-wrapper' => 'input-group',
        'error-class' => 'has-error',
        'invalid-input-class' => 'is-invalid',
        'invalid-feedback' => 'invalid-feedback',
        'check-class' => 'form-check',

        'check-input-class' => 'form-check-input',
        'check-inline-class' => 'form-check form-check-inline',
        'custom-file-label' => 'custom-file-label form-label',
        'custom-file-input-class' => 'custom-file-input form-control',
        'custom-file-wrapper-class' => 'custom-file',

        'input-group-text' => 'input-group-text',
        'input-group-before' => 'input-group-prepend',
        'input-group-after' => 'input-group-append',

        /*
        * --------------------------------------------------------------------------
        * Form Sections
        * --------------------------------------------------------------------------
        */

        'sections' => [
            'column-base' => 'col-md-',
            'row-class' => 'row',
            'full-size-column' => 'col-md-12',
            'header-spacing' => 'mt-2 mb-2',
            'row-alignment-between' => 'd-flex justify-content-between',
            'row-alignment-end' => 'd-flex justify-content-end',
            'button-row' => 'row',
            'button-column' => 'col-md-12',
        ],

        'cards' => [
            'card-body' => 'card-body',
            'card-footer' => 'card-footer',
        ],

        /*
         * --------------------------------------------------------------------------
         * For Horizontal forms
         * --------------------------------------------------------------------------
         *  You may wish to use horizontal forms. If you do, you need to set the
         *  orientation to horizontal, and ensure that your form has the 'form-horizontal'
         *  class.
        */

        'orientation' => 'vertical',
        'horizontal-class' => 'form-horizontal',
        'label-column' => 'col-md-2 col-form-label pt-0',
        'input-column' => 'col-md-10',
    ]

];
