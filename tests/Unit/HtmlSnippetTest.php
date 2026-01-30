<?php

namespace Tests\Unit;

use Grafite\Forms\Html\Div;
use Grafite\Forms\Html\DivClose;
use Grafite\Forms\Html\DivOpen;
use Grafite\Forms\Html\Heading;
use Grafite\Forms\Html\HrTag;
use Grafite\Forms\Html\HtmlSnippet;
use Tests\TestCase;

class HtmlSnippetTest extends TestCase
{
    public function test_html_snippet()
    {
        $snippet = HtmlSnippet::make('<hr>')->toArray();

        $this->assertEquals('html', $snippet['type']);
        $this->assertEquals('<hr>', $snippet['content']);
    }

    public function test_div_open()
    {
        $snippet = DivOpen::make(['class' => 'card'])->toArray();

        $this->assertEquals('html', $snippet['type']);
        $this->assertEquals('card', $snippet['attributes']['class']);
    }

    public function test_div_close()
    {
        $snippet = DivClose::make()->toArray();

        $this->assertEquals('html', $snippet['type']);
    }

    public function test_hr_tag()
    {
        $snippet = HrTag::make()->toArray();

        $this->assertEquals('html', $snippet['type']);
        // $this->assertEquals('<hr>', $snippet['content']);
    }

    public function test_heading_tag()
    {
        $snippet = Heading::make([
            'content' => 'Billing Details',
        ])->toArray();

        $this->assertEquals('html', $snippet['type']);
        $this->assertEquals('Billing Details', $snippet['content']);
    }

    public function test_div_tag()
    {
        $snippet = Div::make([
            'content' => '<p class="foo">Bar</p>',
        ])->toArray();

        $this->assertEquals('html', $snippet['type']);
        $this->assertEquals('<p class="foo">Bar</p>', $snippet['content']);
    }
}
