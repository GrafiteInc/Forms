<?php

namespace Grafite\Forms\Html;

use Illuminate\Support\Str;
use Grafite\Forms\Services\HtmlConfigProcessor;

class HoneyPot extends HtmlSnippet
{
    public static function make($content = null, $name = null)
    {
        $options = [
            'type' => 'html',
            'content' => (string) self::content(),
        ];

        if (is_null($name)) {
            $name = 'honeypot';
        }

        return (new HtmlConfigProcessor($name, $options));
    }

    public static function content($options = [])
    {
        return view('honeypot::honeypotFormFields');
    }
}
