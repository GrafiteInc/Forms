<?php

namespace Grafite\Forms\Parsers;

class HtmlConvertor
{
    private $templates = null;

    public function __construct($templates)
    {
        $this->templates = $templates;
    }

    public function render($blocks)
    {
        $result = [];

        foreach ($blocks as $block) {
            if (array_key_exists($block['type'], $this->templates)) {
                $template = $this->templates[$block['type']];
                $data = $block['data'];
                $result[] = call_user_func_array($template, $data);
            }
        }

        $html = implode($result);

        return $html;
    }
}
