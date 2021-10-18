<?php

namespace Grafite\Forms\Builders;

class AttributeBuilder
{
    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function render($attributes, $name = null, $livewireEnabled = false, $livewireOnKeydown = false)
    {
        $html = [];
        $livewireAttributes = [];

        if ($livewireEnabled) {
            $livewireAttributes['wire:model'] = "data.${name}";
        }

        if ($livewireOnKeydown) {
            $livewireAttributes['wire:keydown.debounce.1000ms'] = 'submit';
        }

        $attributes = array_merge($attributes, $livewireAttributes);

        foreach ((array) $attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if (! is_null($element)) {
                $html[] = $element;
            }
        }

        return implode(' ', $html);
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    public function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            return $value;
        }

        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="' . implode(' ', $value) . '"';
        }

        if (! is_null($value)) {
            return $key . '="' . e($value, false) . '"';
        }
    }
}
