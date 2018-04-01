<?php

use Illuminate\Container\Container as Container;
use Illuminate\Support\Facades\Facade as Facade;
use Grafite\FormMaker\Generators\HtmlGenerator;

class HtmlGeneratorTest extends TestCase
{
    protected $app;
    protected $html;

    public function setUp()
    {
        parent::setUp();

        $this->html = app(HtmlGenerator::class);
    }

    public function testMakeHidden()
    {
        $test = $this->html->makeHidden(['name' => 'test'], 'test', '');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  id="Test" name="test" type="hidden" value="test">', $test);
    }

    public function testMakeHiddenSpecialId()
    {
        $test = $this->html->makeHidden(['id' => 'specialHidden', 'name' => 'test'], 'test', '');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  id="specialHidden" name="test" type="hidden" value="test">', $test);
    }

    public function testMakeText()
    {
        $test = $this->html->makeText([
            'name' => 'test',
            'class' => 'form-control',
            'placeholder' => 'TestText'
        ], 'simple-test', 'data-thing');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<textarea data-thing id="Test" class="form-control" name="test" placeholder="TestText">simple-test</textarea>', $test);
    }

    public function testMakeSelected()
    {
        $test = $this->html->makeSelected([
            'name' => 'test',
            'class' => 'form-control',
            'config' => [
                'options' => [
                    'Admin' => 'admin',
                    'Member' => 'member'
                ]
            ]
        ], 'member', 'data-thing');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<select data-thing id="Test" class="form-control" name="test"><option value="admin" >Admin</option><option value="member" selected>Member</option></select>', $test);
    }

    public function testMakeCheckbox()
    {
        $test = $this->html->makeCheckbox([
            'name' => 'test',
            'class' => 'customClass',
        ], 'selected', '');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  id="Test" selected type="checkbox" name="test" class="customClass">', $test);
    }

    public function testMakeRadio()
    {
        $test = $this->html->makeRadio([
            'name' => 'test',
            'class' => 'customClass',
        ], 'selected', '');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  id="Test" selected type="radio" name="test" class="customClass">', $test);
    }

    public function testMakeInputString()
    {
        $test = $this->html->makeHTMLInputString([
            'config' => [
                'custom' => 'data-stuff'
            ],
            'placeholder' => 'wtf',
            'inputType' => 'text',
            'type' => 'text',
            'populated' => true,
            'name' => 'test',
            'class' => 'form-control',
            'objectValue' => 'sample Test'
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input data-stuff id="Test" class="form-control" type="text" name="test"   value="sample Test" placeholder="wtf">', $test);
    }
}