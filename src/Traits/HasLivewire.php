<?php

namespace Grafite\Forms\Traits;

trait HasLivewire
{
    /**
     * Set if the form is using livewire
     *
     * @param bool $livewire
     */
    public function setLivewire($livewire)
    {
        $this->withLivewire = $livewire;

        return $this;
    }

    /**
     * Set if the form is using livewire on keydown
     *
     * @param bool $onKeyDown
     */
    public function setLivewireOnKeydown($onKeyDown)
    {
        $this->livewireOnKeydown = $onKeyDown;

        return $this;
    }
}
