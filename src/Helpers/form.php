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

if (! function_exists('tags_values')) {
    function tags_values(string $tags)
    {
        return collect(json_decode($tags))->pluck('value')->values()->toArray();
    }
}

if (! function_exists('tags_to_string')) {
    function tags_to_string(array $tags)
    {
        return collect($tags)->values()->implode(', ');
    }
}

