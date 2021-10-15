<?php

use Grafite\Forms\Html\Div;
use Grafite\Forms\Html\Link;
use Grafite\Forms\Html\Span;
use Grafite\Forms\Html\HrTag;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Html\Button;
use Grafite\Forms\Html\Heading;
use Grafite\Forms\Html\DivClose;

class HtmlConfigProcessorTest extends TestCase
{
    public function testHref()
    {
        $html = Link::make('Click here for more info!')
            ->id('batmanLink')
            ->href('batman.com');

        $this->assertEquals('<a id="batmanLink" href="batman.com">Click here for more info!</a>', (string) $html);
    }

    public function testDiv()
    {
        $html = Div::make('<p>Something goes here</p>')
            ->id('div-123');

        $this->assertEquals('<div id="div-123"><p>Something goes here</p></div>', (string) $html);
    }

    public function testHeading()
    {
        $html = Heading::make('Heading!')->level(1)->cssClass('hero');

        $this->assertEquals('<h1 class="hero">Heading!</h1>', (string) $html);
    }

    public function testCloseDiv()
    {
        $html = DivClose::make();

        $this->assertEquals('</div>', (string) $html);
    }

    public function testSpan()
    {
        $html = Span::make('who')->cssClass('are-you');

        $this->assertEquals('<span class="are-you">who</span>', (string) $html);
    }

    public function testButton()
    {
        $html = Button::make('click Me!')->cssClass('are-you');

        $this->assertEquals('<button class="are-you">click Me!</button>', (string) $html);
    }

    public function testHrTag()
    {
        $html = HrTag::make()->cssClass('are-you');

        $this->assertEquals('<hr class="are-you">', (string) $html);
    }

    public function testButtonOnClick()
    {
        $html = Button::make('click Me!')->cssClass('are-you')->onClick('window.location.reload();');

        $this->assertEquals('<button class="are-you" onclick="window.location.reload();">click Me!</button>', (string) $html);
    }
}
