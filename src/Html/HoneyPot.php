<?php

namespace Grafite\Forms\Html;

class HoneyPot extends HtmlSnippet
{
    public static function render($options = [])
    {
        return view('honeypot::honeypotFormFields');
    }
}
