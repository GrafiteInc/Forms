<?php

namespace Grafite\Forms\Builders;

use Illuminate\Support\Str;

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
            if (
                in_array(strtolower($key), $this->validHtmlAttributes())
                || Str::of($key)->startsWith('data-')
                || Str::of($key)->startsWith('wire:')
            ) {
                $element = $this->attributeElement($key, $value);

                if (! is_null($element)) {
                    $html[] = $element;
                }
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

    public function validHtmlAttributes()
    {
        return [
            'accept',
            'accept-charset',
            'accesskey',
            'action',
            'align',
            'alt',
            'async',
            'autocomplete',
            'autofocus',
            'autoplay',
            'bgcolor',
            'border',
            'charset',
            'checked',
            'cite',
            'class',
            'color',
            'cols',
            'colspan',
            'content',
            'contenteditable',
            'controls',
            'coords',
            'data',
            'datetime',
            'default',
            'defer',
            'dir',
            'dirname',
            'disabled',
            'download',
            'draggable',
            'enctype',
            'for',
            'form',
            'formaction',
            'headers',
            'height',
            'hidden',
            'high',
            'href',
            'hreflang',
            'http-equiv',
            'id',
            'ismap',
            'kind',
            'label',
            'lang',
            'list',
            'loop',
            'low',
            'max',
            'maxlength',
            'media',
            'method',
            'min',
            'multiple',
            'muted',
            'name',
            'novalidate',
            'onabort',
            'onafterprint',
            'onbeforeprint',
            'onbeforeunload',
            'onblur',
            'oncanplay',
            'oncanplaythrough',
            'onchange',
            'onclick',
            'oncontextmenu',
            'oncopy',
            'oncuechange',
            'oncut',
            'ondblclick',
            'ondrag',
            'ondragend',
            'ondragenter',
            'ondragleave',
            'ondragover',
            'ondragstart',
            'ondrop',
            'ondurationchange',
            'onemptied',
            'onended',
            'onerror',
            'onfocus',
            'onhashchange',
            'oninput',
            'oninvalid',
            'onkeydown',
            'onkeypress',
            'onkeyup',
            'onload',
            'onloadeddata',
            'onloadedmetadata',
            'onloadstart',
            'onmousedown',
            'onmousemove',
            'onmouseout',
            'onmouseover',
            'onmouseup',
            'onmousewheel',
            'onoffline',
            'ononline',
            'onpagehide',
            'onpageshow',
            'onpaste',
            'onpause',
            'onplay',
            'onplaying',
            'onpopstate',
            'onprogress',
            'onratechange',
            'onreset',
            'onresize',
            'onscroll',
            'onsearch',
            'onseeked',
            'onseeking',
            'onselect',
            'onstalled',
            'onstorage',
            'onsubmit',
            'onsuspend',
            'ontimeupdate',
            'ontoggle',
            'onunload',
            'onvolumechange',
            'onwaiting',
            'onwheel',
            'open',
            'optimum',
            'pattern',
            'placeholder',
            'poster',
            'preload',
            'readonly',
            'rel',
            'required',
            'reversed',
            'rows',
            'rowspan',
            'sandbox',
            'scope',
            'selected',
            'shape',
            'size',
            'sizes',
            'span',
            'spellcheck',
            'src',
            'srcdoc',
            'srclang',
            'srcset',
            'start',
            'step',
            'style',
            'tabindex',
            'target',
            'title',
            'translate',
            'type',
            'usemap',
            'value',
            'width',
            'wrap',
        ];
    }
}
