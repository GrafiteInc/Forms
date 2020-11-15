<?php

namespace Grafite\Forms\Traits;

trait HasForm
{
    public function form()
    {
        $form = app($this->form);

        if ($this->exists) {
            $form->setInstance($this);
        }

        return $form;
    }
}
