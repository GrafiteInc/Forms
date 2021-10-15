<?php

use Grafite\Forms\Html\Div;
use Grafite\Forms\Html\HrTag;
use Grafite\Forms\Html\DivOpen;
use Grafite\Forms\Html\Heading;
use Grafite\Forms\Html\DivClose;
use Grafite\Forms\Html\HtmlSnippet;

class HtmlSnippetTest extends TestCase
{
    public function testHtmlSnippet()
    {
        $snippet = HtmlSnippet::make('<hr>')->toArray();

        $this->assertEquals('html', $snippet['type']);
        $this->assertEquals('<hr>', $snippet['content']);
    }

    public function testDivOpen()
    {
        $snippet = DivOpen::make(['class' => 'card'])->toArray();

        $this->assertEquals('html', $snippet['type']);
        $this->assertEquals('card', $snippet['attributes']['class']);
    }

    public function testDivClose()
    {
        $snippet = DivClose::make()->toArray();

        $this->assertEquals('html', $snippet['type']);
    }

    public function testHrTag()
    {
        $snippet = HrTag::make()->toArray();

        $this->assertEquals('html', $snippet['type']);
        // $this->assertEquals('<hr>', $snippet['content']);
    }

    public function testHeadingTag()
    {
        $snippet = Heading::make([
            'content' => 'Billing Details'
        ])->toArray();

        $this->assertEquals('html', $snippet['type']);
        $this->assertEquals('Billing Details', $snippet['content']);
    }

    public function testDivTag()
    {
        $snippet = Div::make([
            'content' => '<p class="foo">Bar</p>'
        ])->toArray();

        $this->assertEquals('html', $snippet['type']);
        $this->assertEquals('<p class="foo">Bar</p>', $snippet['content']);
    }
}
