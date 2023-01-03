<?php

namespace Grafite\Forms\Parsers;

class Editor implements FieldParser
{
    public $html;

    public function parse($content)
    {
        if (is_null($content)) {
            return $this;
        }

        $blocks = json_decode($content, true)['blocks'];

        $this->handler($blocks);

        return $this;
    }

    public function handler($content)
    {
        $convertor = new HtmlConvertor([
            'raw' => function ($html) {
                return $html;
            },
            'header' => function ($text, $level) {
                return "<h{$level}>{$text}</h{$level}>";
            },
            'paragraph' => function ($text) {
                return "<p>{$text}</p>";
            },
            'delimiter' => function () {
                return '<div><span>* * *</span></div>';
            },
            'image' => function ($file, $caption) {
                return "<img src=\"{$file['url']}\" title=\"{$caption}\" alt=\"{$caption}\">";
            },
            'quote' => function ($text, $caption, $alignment) {
                return "<figure><blockquote style=\"text-align: {$alignment};\">{$text}</blockquote><figcaption>{$caption}</figcaption></figure>";
            },
            'list' => function ($items, $style) {
                $listItems = '';
                $listStyle = 'u';

                if ($style === 'ordered') {
                    $listStyle = 'o';
                }

                foreach ($items as $item) {
                    $listItems .= "<li>{$item}</li>";
                }

                return "<{$listStyle}l>{$listItems}</{$listStyle}l>";
            },
            'table' => function ($content) {
                $contents = '';

                foreach ($content as $row) {
                    $contents .= '<tr>';

                    foreach ($row as $columns) {
                        $contents .= "<td>{$columns}</td>";
                    }

                    $contents .= '</tr>';
                }

                return "<table>{$contents}</table>";
            },
            'checklist' => function ($items) {
                $checklist = '';

                foreach ($items as $item) {
                    $checked = '';

                    if ($item['checked']) {
                        $checked = 'checked';
                    }

                    $text = $item['text'];

                    $checklist .=
                    "<div><input disabled type=\"checkbox\" {$checked}><label>{$text}</label></div>";
                }

                return $checklist;
            },
            'code' => function ($code) {
                $code = htmlspecialchars($code);

                return "<pre><code>{$code}</code></pre>";
            },
        ]);

        $this->html = $convertor->render($content);
    }

    public function render()
    {
        return $this->html;
    }
}
