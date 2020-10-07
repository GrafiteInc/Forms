<?php

namespace Grafite\Forms\Traits;

trait HasErrorBag
{
    public function setErrorBag($bag)
    {
        $this->errorBag = $bag;

        return $this;
    }
}
