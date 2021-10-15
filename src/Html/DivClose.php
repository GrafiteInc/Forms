<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class DivClose extends HtmlSnippet
{
    public static function render($options = [])
    {
        return '</div>';
    }
}
