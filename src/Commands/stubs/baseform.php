<?php

namespace App\Http\Forms;

use Grafite\FormMaker\Forms\BaseForm;

class {form} extends BaseForm
{
    /**
     * The form route
     *
     * If you need to inject a parameter
     * to the route, then use the `setRoute`
     * method.
     *
     * @var string
     */
    public $route = '';

    /**
     * Buttons and values
     *
     * @var array
     */
    public $buttons = [
        'submit' => 'Send'
    ];

    /**
     * Set the desired fields for the form
     *
     * @return array
     */
    public function fields()
    {
        return [
            //
        ];
    }
}
