<?php

namespace DummyNamespace;

use Grafite\Forms\Forms\BaseForm;

class DummyClass extends BaseForm
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
