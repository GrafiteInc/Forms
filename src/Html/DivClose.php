<?php

namespace Grafite\Forms\Html;

use Grafite\Forms\Html\HtmlSnippet;

class DivClose extends HtmlSnippet
{
    public static function content($options = [])
    {
        return '</div>';
    }
}
