<?php

use Grafite\FormMaker\Builders\FieldBuilder;

class FieldBuilderTest extends TestCase
{
    protected $app;
    protected $builder;

    public function setUp(): void
    {
        parent::setUp();

        $this->builder = app(FieldBuilder::class);
    }

    public function testMakeInput()
    {
        $test = $this->builder->makeInput('text', 'name', 'superman', []);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  name="name" type="text" value="superman">', $test);
    }

    public function testMakeButton()
    {
        $test = $this->builder->button('Clicker');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<button type="button">Clicker</button>', $test);
    }

    public function testMakeSubmit()
    {
        $test = $this->builder->submit('Save');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  name="" type="submit" value="Save">', $test);
    }

    public function testAttributes()
    {
        $test = $this->builder->attributes([
            'placeholder' => 'thing'
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals(' placeholder="thing"', $test);
    }

    public function testAttributeElement()
    {
        $test = $this->builder->attributeElement('placeholder', 'thing');

        $this->assertTrue(is_string($test));
        $this->assertEquals('placeholder="thing"', $test);
    }

    public function testMakeCustomFile()
    {
        $test = $this->builder->makeCustomFile('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="custom-file"><input  id="Avatar" class="foo-class" class="custom-file-input" type="file" name="avatar"><label class="custom-file-label" for="Avatar">Choose file</label></div>', $test);
    }

    public function testMakeTextarea()
    {
        $test = $this->builder->makeTextarea('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<textarea  id="Avatar" class="foo-class" name="avatar"></textarea>', $test);
    }

    public function testMakeCheckboxInline()
    {
        $test = $this->builder->makeCheckboxInline('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  id="Avatar" class="foo-class" type="checkbox" name="avatar" >', $test);
    }

    public function testMakeRadioInline()
    {
        $test = $this->builder->makeRadioInline('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  id="Avatar" class="foo-class" type="radio" name="avatar" >', $test);
    }

    public function testMakeSelect()
    {
        $test = $this->builder->makeSelect('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ],
            'options' => [
                'matt' => 'Matt',
                'cassandra' => 'Cassandra',
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<select  id="Avatar" class="foo-class" name="avatar"><option value="Matt" >matt</option><option value="Cassandra" >cassandra</option></select>', $test);
    }

    public function testMakeSelectWithValue()
    {
        $test = $this->builder->makeSelect('avatar', 'matt', [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ],
            'options' => [
                'Matt' => 'matt',
                'Cassandra' => 'cassandra',
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<select  id="Avatar" class="foo-class" name="avatar"><option value="matt" selected>Matt</option><option value="cassandra" >Cassandra</option></select>', $test);
    }

    public function testMakeCheckInput()
    {
        $test = $this->builder->makeCheckInput('avatar', null, [
            'type' => 'radio',
            'label' => 'foo',
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-check"><input  id="Avatar" class="form-check-input" type="checkbox" name="avatar" ><label class="form-check-label">foo</label></div>', $test);
    }

    public function testMakeCheckbox()
    {
        $test = $this->builder->makeCheckbox('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  id="Avatar" class="foo-class" type="checkbox" name="avatar" >', $test);
    }

    public function testMakeRadio()
    {
        $test = $this->builder->makeRadio('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  id="Avatar" class="foo-class" type="radio" name="avatar" >', $test);
    }

    public function testMakeRelationship()
    {
        app(Job::class)->create([
            'name' => 'BlackSmith',
        ]);
        app(Job::class)->create([
            'name' => 'Police',
        ]);
        app(Job::class)->create([
            'name' => 'Brogrammer',
        ]);

        $test = $this->builder->makeRelationship('avatar', null, [
            'model' => Job::class,
            'options' => app(Job::class)->all()->pluck('id', 'name')->toArray(),
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class'
            ]
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<select  id="Avatar" class="foo-class" name="avatar"><option value="1" >BlackSmith</option><option value="2" >Police</option><option value="3" >Brogrammer</option></select>', $test);
    }

    public function testIsChecked()
    {
        $test = $this->builder->isChecked('foo', true, []);
        $this->assertEquals('checked', $test);

        $test = $this->builder->isChecked('foo', 'on', []);
        $this->assertEquals('checked', $test);

        $test = $this->builder->isChecked('foo', 1, []);
        $this->assertEquals('checked', $test);

        $test = $this->builder->isChecked('foo', null, []);
        $this->assertEquals('', $test);

        $test = $this->builder->isChecked('foo', ['foo'], []);
        $this->assertEquals('checked', $test);

        $test = $this->builder->isChecked('foo', null, [
            'attributes' => [
                'value' => 'foo'
            ]
        ]);
        $this->assertEquals('', $test);

        $test = $this->builder->isChecked('foo', 'foo', [
            'attributes' => [
                'value' => 'foo'
            ]
        ]);
        $this->assertEquals('checked', $test);
    }
}