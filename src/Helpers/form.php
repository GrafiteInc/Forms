<?php

use Grafite\Forms\Forms\Form;

if (! function_exists('form')) {
    function form($model = null)
    {
        if (! is_null($model)) {
            return app($model->form)->setInstance($model);
        }

        return app(Form::class);
    }
}
