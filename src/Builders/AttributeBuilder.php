<?php

namespace Grafite\Forms\Builders;

class AttributeBuilder
{
    /**
     * Valid HTML attributes as a lookup map for O(1) `isset()` checks.
     */
    private const VALID_HTML_ATTRIBUTES = [
        'accept' => true,
        'accept-charset' => true,
        'accesskey' => true,
        'action' => true,
        'align' => true,
        'alt' => true,
        'async' => true,
        'autocomplete' => true,
        'autofocus' => true,
        'autoplay' => true,
        'bgcolor' => true,
        'border' => true,
        'charset' => true,
        'checked' => true,
        'cite' => true,
        'class' => true,
        'color' => true,
        'cols' => true,
        'colspan' => true,
        'content' => true,
        'contenteditable' => true,
        'controls' => true,
        'coords' => true,
        'data' => true,
        'datetime' => true,
        'default' => true,
        'defer' => true,
        'dir' => true,
        'dirname' => true,
        'disabled' => true,
        'download' => true,
        'draggable' => true,
        'enctype' => true,
        'for' => true,
        'form' => true,
        'formaction' => true,
        'headers' => true,
        'height' => true,
        'hidden' => true,
        'high' => true,
        'href' => true,
        'hreflang' => true,
        'http-equiv' => true,
        'id' => true,
        'ismap' => true,
        'kind' => true,
        'label' => true,
        'lang' => true,
        'list' => true,
        'loop' => true,
        'low' => true,
        'max' => true,
        'maxlength' => true,
        'media' => true,
        'method' => true,
        'min' => true,
        'multiple' => true,
        'muted' => true,
        'name' => true,
        'novalidate' => true,
        'onabort' => true,
        'onafterprint' => true,
        'onbeforeprint' => true,
        'onbeforeunload' => true,
        'onblur' => true,
        'oncanplay' => true,
        'oncanplaythrough' => true,
        'onchange' => true,
        'onclick' => true,
        'oncontextmenu' => true,
        'oncopy' => true,
        'oncuechange' => true,
        'oncut' => true,
        'ondblclick' => true,
        'ondrag' => true,
        'ondragend' => true,
        'ondragenter' => true,
        'ondragleave' => true,
        'ondragover' => true,
        'ondragstart' => true,
        'ondrop' => true,
        'ondurationchange' => true,
        'onemptied' => true,
        'onended' => true,
        'onerror' => true,
        'onfocus' => true,
        'onhashchange' => true,
        'oninput' => true,
        'oninvalid' => true,
        'onkeydown' => true,
        'onkeypress' => true,
        'onkeyup' => true,
        'onload' => true,
        'onloadeddata' => true,
        'onloadedmetadata' => true,
        'onloadstart' => true,
        'onmousedown' => true,
        'onmousemove' => true,
        'onmouseout' => true,
        'onmouseover' => true,
        'onmouseup' => true,
        'onmousewheel' => true,
        'onoffline' => true,
        'ononline' => true,
        'onpagehide' => true,
        'onpageshow' => true,
        'onpaste' => true,
        'onpause' => true,
        'onplay' => true,
        'onplaying' => true,
        'onpopstate' => true,
        'onprogress' => true,
        'onratechange' => true,
        'onreset' => true,
        'onresize' => true,
        'onscroll' => true,
        'onsearch' => true,
        'onseeked' => true,
        'onseeking' => true,
        'onselect' => true,
        'onstalled' => true,
        'onstorage' => true,
        'onsubmit' => true,
        'onsuspend' => true,
        'ontimeupdate' => true,
        'ontoggle' => true,
        'onunload' => true,
        'onvolumechange' => true,
        'onwaiting' => true,
        'onwheel' => true,
        'open' => true,
        'optimum' => true,
        'pattern' => true,
        'placeholder' => true,
        'poster' => true,
        'preload' => true,
        'readonly' => true,
        'rel' => true,
        'required' => true,
        'reversed' => true,
        'rows' => true,
        'rowspan' => true,
        'sandbox' => true,
        'scope' => true,
        'selected' => true,
        'shape' => true,
        'size' => true,
        'sizes' => true,
        'span' => true,
        'spellcheck' => true,
        'src' => true,
        'srcdoc' => true,
        'srclang' => true,
        'srcset' => true,
        'start' => true,
        'step' => true,
        'style' => true,
        'tabindex' => true,
        'target' => true,
        'title' => true,
        'translate' => true,
        'type' => true,
        'usemap' => true,
        'value' => true,
        'width' => true,
        'wrap' => true,
    ];

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     * @return string
     */
    public function render($attributes, $name = null, $livewireEnabled = false, $livewireOnKeydown = false, $livewireOnChange = false)
    {
        $html = [];
        $livewireAttributes = [];

        if ($livewireEnabled) {
            $livewireAttributes['wire:model'] = "data.{$name}";
        }

        if ($livewireOnKeydown) {
            $livewireAttributes['wire:keydown.debounce.1000ms'] = 'submit';
        }

        if ($livewireOnChange) {
            $livewireAttributes['wire:change.debounce.600ms'] = 'submit';
        }

        $attributes = array_merge($attributes, $livewireAttributes);

        foreach ((array) $attributes as $key => $value) {
            if (
                isset(self::VALID_HTML_ATTRIBUTES[strtolower($key)])
                || str_starts_with($key, 'data-')
                || str_starts_with($key, 'wire:')
            ) {
                $element = $this->attributeElement($key, $value);

                if (! is_null($element)) {
                    $html[] = $element;
                }
            }
        }

        return implode(' ', array_unique($html));
    }

    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
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
            return 'class="'.implode(' ', $value).'"';
        }

        if (! is_null($value)) {
            return $key.'="'.e($value, false).'"';
        }
    }

    public function validHtmlAttributes()
    {
        return array_keys(self::VALID_HTML_ATTRIBUTES);
    }
}
