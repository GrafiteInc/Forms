<?php

if (!function_exists('form')) {
    function form()
    {
        return app('Grafite\FormMaker\Forms\Form');
    }
}
