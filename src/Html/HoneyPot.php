<?php

namespace Grafite\Forms\Html;

class HoneyPot extends HtmlSnippet
{
    public static function make($content = null, $name = null)
    {
        return [
            'honeypot' => [
                'type' => 'html',
                'content' => (string) self::content(),
            ],
        ];
    }
    public static function content($options = [])
    {
        return view('honeypot::honeypotFormFields');
    }
}
