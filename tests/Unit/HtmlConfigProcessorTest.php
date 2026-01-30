<?php

namespace Tests\Unit;

use Grafite\Forms\Html\Button;
use Grafite\Forms\Html\Div;
use Grafite\Forms\Html\DivClose;
use Grafite\Forms\Html\Heading;
use Grafite\Forms\Html\HrTag;
use Grafite\Forms\Html\Link;
use Grafite\Forms\Html\Span;
use Tests\TestCase;

class HtmlConfigProcessorTest extends TestCase
{
    public function test_href()
    {
        $html = Link::make('Click here for more info!')
            ->id('batmanLink')
            ->href('batman.com');

        $this->assertEquals('<a id="batmanLink" href="batman.com">Click here for more info!</a>', (string) $html);
    }

    public function test_div()
    {
        $html = Div::make('<p>Something goes here</p>')
            ->id('div-123');

        $this->assertEquals('<div id="div-123"><p>Something goes here</p></div>', (string) $html);
    }

    public function test_heading()
    {
        $html = Heading::make('Heading!')->level(1)->cssClass('hero');

        $this->assertEquals('<h1 class="hero">Heading!</h1>', (string) $html);
    }

    public function test_close_div()
    {
        $html = DivClose::make();

        $this->assertEquals('</div>', (string) $html);
    }

    public function test_span()
    {
        $html = Span::make('who')->cssClass('are-you');

        $this->assertEquals('<span class="are-you">who</span>', (string) $html);
    }

    public function test_button()
    {
        $html = Button::make('click Me!')->cssClass('are-you');

        $this->assertEquals('<button class="are-you">click Me!</button>', (string) $html);
    }

    public function test_hr_tag()
    {
        $html = HrTag::make()->cssClass('are-you');

        $this->assertEquals('<hr class="are-you">', (string) $html);
    }

    public function test_button_on_click()
    {
        $html = Button::make('click Me!')->cssClass('are-you')->onClick('window.location.reload();');

        $this->assertEquals('<button class="are-you" data-formsjs-onclick="window.location.reload();">click Me!</button>', (string) $html);
    }

    public function test_original()
    {
        $snippet = Link::make([
            'content' => 'Forgot Password?',
            'class' => 'd-block mt-3 text-right',
            'href' => 'foo-bar',
        ], 'forgot_password');

        $this->assertEquals('<a class="d-block mt-3 text-right" href="foo-bar">Forgot Password?</a>', (string) $snippet);
    }
}
