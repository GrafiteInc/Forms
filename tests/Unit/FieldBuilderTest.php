<?php

namespace Tests\Unit;

use Grafite\Forms\Builders\FieldBuilder;
use Tests\TestCase;

class FieldBuilderTest extends TestCase
{
    protected $app;

    protected $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = app(FieldBuilder::class);
    }

    public function test_make_input()
    {
        $test = $this->builder->makeInput('text', 'name', 'superman', []);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  name="name" type="text" value="superman">', $test);
    }

    public function test_make_field()
    {
        $test = $this->builder->makeField('datepicker', 'name', 'superman', []);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<datepicker  name="name" value="superman"></datepicker>', $test);
    }

    public function test_make_button()
    {
        $test = $this->builder->button('Clicker');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<button type="button">Clicker</button>', $test);
    }

    public function test_make_submit()
    {
        $test = $this->builder->submit('Save');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input  name="" type="submit" value="Save">', $test);
    }

    public function test_make_custom_file()
    {
        $test = $this->builder->makeCustomFile('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
            'multiple' => true,
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="custom-file"><input id="Avatar" class="foo-class custom-file-input" type="file" name="avatar[]"><label class="custom-file-label" for="Avatar">Choose files</label></div>', $test);
    }

    public function test_make_textarea()
    {
        $test = $this->builder->makeTextarea('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<textarea id="Avatar" class="foo-class" name="avatar"></textarea>', $test);
    }

    public function test_make_checkbox_inline()
    {
        $test = $this->builder->makeCheckboxInline('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input id="Avatar" class="foo-class" type="checkbox" name="avatar">', $test);
    }

    public function test_make_radio_inline()
    {
        $test = $this->builder->makeRadioInline('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input id="Avatar" class="foo-class" type="radio" name="avatar">', $test);
    }

    public function test_make_select()
    {
        $test = $this->builder->makeSelect('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
            'options' => [
                'matt' => 'Matt',
                'cassandra' => 'Cassandra',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<select id="Avatar" class="foo-class" name="avatar"><option value="Matt">matt</option><option value="Cassandra">cassandra</option></select>', $test);
    }

    public function test_make_select_with_value()
    {
        $test = $this->builder->makeSelect('avatar', 'matt', [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
            'options' => [
                'Matt' => 'matt',
                'Cassandra' => 'cassandra',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<select id="Avatar" class="foo-class" name="avatar"><option value="matt" selected>Matt</option><option value="cassandra">Cassandra</option></select>', $test);
    }

    public function test_make_check_input()
    {
        $test = $this->builder->makeCheckInput('avatar', null, [
            'type' => 'radio',
            'label' => 'foo',
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-check"><input id="Avatar" class="form-check-input foo-class" type="radio" name="avatar"><label class="form-check-label" for="Avatar">foo</label></div>', $test);
    }

    public function test_make_checkbox()
    {
        $test = $this->builder->makeCheckbox('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input id="Avatar" class="foo-class" type="checkbox" name="avatar">', $test);
    }

    public function test_make_radio()
    {
        $test = $this->builder->makeRadio('avatar', null, [
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<input id="Avatar" class="foo-class" type="radio" name="avatar">', $test);
    }

    public function test_make_relationship_with_none()
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
            'null_value' => true,
            'null_label' => 'foo',
            'attributes' => [
                'id' => 'Avatar',
                'class' => 'foo-class',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<select id="Avatar" class="foo-class" name="avatar"><option value="" selected>foo</option><option value="1">BlackSmith</option><option value="2">Police</option><option value="3">Brogrammer</option></select>', $test);
    }

    public function test_make_relationship()
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
                'class' => 'foo-class',
            ],
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<select id="Avatar" class="foo-class" name="avatar"><option value="1">BlackSmith</option><option value="2">Police</option><option value="3">Brogrammer</option></select>', $test);
    }

    public function test_is_checked()
    {
        $test = $this->builder->isChecked('foo', true, []);
        $this->assertEquals(' checked', $test);

        $test = $this->builder->isChecked('foo', 'on', []);
        $this->assertEquals(' checked', $test);

        $test = $this->builder->isChecked('foo', 1, []);
        $this->assertEquals(' checked', $test);

        $test = $this->builder->isChecked('foo', null, []);
        $this->assertEquals('', $test);

        $test = $this->builder->isChecked('foo', ['foo'], []);
        $this->assertEquals(' checked', $test);

        $test = $this->builder->isChecked('foo', null, [
            'attributes' => [
                'value' => 'on',
            ],
        ]);
        $this->assertEquals(' checked', $test);

        $test = $this->builder->isChecked('foo', 'foo', [
            'attributes' => [
                'value' => 'foo',
            ],
        ]);
        $this->assertEquals(' checked', $test);
    }
}
