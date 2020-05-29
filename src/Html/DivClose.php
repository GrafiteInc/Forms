<?php

namespace Grafite\FormMaker\Html;

use Grafite\FormMaker\Html\HtmlSnippet;

class DivClose extends HtmlSnippet
{
    public static function content($options = [])
    {
        return '</div>';
    }
}
