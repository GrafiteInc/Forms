<?php

use Grafite\FormMaker\Html\HrTag;
use Grafite\FormMaker\Html\DivOpen;
use Grafite\FormMaker\Html\DivClose;
use Grafite\FormMaker\Html\HtmlSnippet;

class HtmlSnippetTest extends TestCase
{
    public function testHtmlSnippet()
    {
        $snippet = HtmlSnippet::make('<hr>');

        $keys = array_keys($snippet);

        $this->assertEquals('html', $snippet[$keys[0]]['type']);
        $this->assertEquals('<hr>', $snippet[$keys[0]]['content']);
    }

    public function testDivOpen()
    {
        $snippet = DivOpen::make(['class' => 'card']);

        $keys = array_keys($snippet);

        $this->assertEquals('html', $snippet[$keys[0]]['type']);
        $this->assertEquals('<div class="card">', $snippet[$keys[0]]['content']);
    }

    public function testDivClose()
    {
        $snippet = DivClose::make();

        $keys = array_keys($snippet);

        $this->assertEquals('html', $snippet[$keys[0]]['type']);
        $this->assertEquals('</div>', $snippet[$keys[0]]['content']);
    }

    public function testHrTag()
    {
        $snippet = HrTag::make();

        $keys = array_keys($snippet);

        $this->assertEquals('html', $snippet[$keys[0]]['type']);
        $this->assertEquals('<hr>', $snippet[$keys[0]]['content']);
    }
}
