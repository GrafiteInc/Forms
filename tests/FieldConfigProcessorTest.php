<?php

use Grafite\Forms\Fields\File;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Fields\Select;
use Grafite\Forms\Fields\Checkbox;

class FieldConfigProcessorTest extends TestCase
{
    public function testLabel()
    {
        $field = Text::make('field')->label('FooBar');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">FooBar</label><input  class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testValue()
    {
        $field = Text::make('field')->value('FooBar');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" name="field" type="text" value="FooBar"></div>', (string) $field);
    }

    public function testRequired()
    {
        $field = Text::make('field')->required();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" required name="field" type="text" value=""></div>', (string) $field);
    }

    public function testData()
    {
        $field = Text::make('field')->data('user', 123)->data('draggable', 'true');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" data-user="123" data-draggable="true" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testTitle()
    {
        $field = Text::make('field')->title('user-field');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" title="user-field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testStyle()
    {
        $field = Text::make('field')->style('color: #f00;');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" style="color: #f00;" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testId()
    {
        $field = Text::make('field')->id('superman');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="superman">Field</label><input  class="form-control" id="superman" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testAutocomplete()
    {
        $field = Text::make('field')->autocomplete();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" autocomplete name="field" type="text" value=""></div>', (string) $field);
    }

    public function testAutofocus()
    {
        $field = Text::make('field')->autofocus();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" autofocus name="field" type="text" value=""></div>', (string) $field);
    }

    public function testMultiple()
    {
        $field = Text::make('field')->multiple();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" multiple name="field" type="text" value=""></div>', (string) $field);
    }

    public function testStep()
    {
        $field = Text::make('field')->step(5);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" step="5" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testPattern()
    {
        $field = Text::make('field')->pattern('^[A-Z0-9+_.-]+@[A-Z0-9.-]+$');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" pattern="^[A-Z0-9+_.-]+@[A-Z0-9.-]+$" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testMax()
    {
        $field = Text::make('field')->max(4);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" max="4" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testMin()
    {
        $field = Text::make('field')->min(4);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" min="4" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testMaxlength()
    {
        $field = Text::make('field')->maxlength(4);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" maxlength="4" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testSize()
    {
        $field = Text::make('field')->size(4);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" size="4" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testDisabled()
    {
        $field = Text::make('field')->disabled();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" disabled name="field" type="text" value=""></div>', (string) $field);
    }

    public function testReadonly()
    {
        $field = Text::make('field')->readonly();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" readonly name="field" type="text" value=""></div>', (string) $field);
    }

    public function testTemplate()
    {
        $field = Text::make('field')->template('{label}{field}');

        $this->assertEquals('<label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" name="field" type="text" value="">', (string) $field);
    }

    public function testView()
    {
        $field = Text::make('field')->view('test-view');

        $this->assertEquals('<div><label class="control-label" for="Field">Field</label></div>
<div><input  class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testCssClass()
    {
        $field = Text::make('field')->cssClass('foo-bar-baz');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="foo-bar-baz" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testLabelClass()
    {
        $field = Text::make('field')->labelClass('foo-bar-baz');

        $this->assertEquals('<div class="form-group"><label class="foo-bar-baz" for="Field">Field</label><input  class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testTableClass()
    {
        $field = Text::make('field')->tableClass('foo-bar-baz')->toArray();

        $this->assertEquals('foo-bar-baz', $field['table_class']);
    }

    public function testHidden()
    {
        $field = Text::make('field')->hidden()->toArray();

        $this->assertEquals(false, $field['visible']);
    }

    public function testVisible()
    {
        $field = Text::make('field')->visible()->toArray();

        $this->assertEquals(true, $field['visible']);
    }

    public function testNullValue()
    {
        $field = Select::make('field')->selectOptions([
            'Superman' => 1,
            'Batman' => 2,
            'Ninja' => 3,
        ])->canSelectNone();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><select  class="form-control" id="Field" name="field"><option value="" selected>None</option><option value="1" >Superman</option><option value="2" >Batman</option><option value="3" >Ninja</option></select></div>', (string) $field);
    }

    public function testNullLabel()
    {
        $field = Select::make('field')->selectOptions([
            'Superman' => 1,
            'Batman' => 2,
            'Ninja' => 3,
        ])->canSelectNone()->noneLabel('Nada');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><select  class="form-control" id="Field" name="field"><option value="" selected>Nada</option><option value="1" >Superman</option><option value="2" >Batman</option><option value="3" >Ninja</option></select></div>', (string) $field);
    }

    public function testSortable()
    {
        $field = Text::make('field')->sortable()->toArray();

        $this->assertEquals(true, $field['sortable']);
    }

    public function testWrapper()
    {
        $field = Text::make('field')->groupClass('foo-bar');

        $this->assertEquals('<div class="foo-bar"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testWithoutWrapper()
    {
        $field = Text::make('field')->ungrouped();

        $this->assertEquals('<label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" name="field" type="text" value="">', (string) $field);
    }

    public function testWithoutLabel()
    {
        $field = Text::make('field')->ungrouped()->withoutLabel();

        $this->assertEquals('<input  class="form-control" id="Field" name="field" type="text" value="">', (string) $field);
    }

    public function testOnlyField()
    {
        $field = Text::make('field')->onlyField();

        $this->assertEquals('<input  class="form-control" id="Field" name="field" type="text" value="">', (string) $field);
    }

    public function testLegend()
    {
        $field = Checkbox::make('dark_mode')->legend('Dark Mode');

        $this->assertEquals('<div class="form-group"><div class="form-check"><input  class="form-check-input" id="Dark_mode" type="checkbox" name="dark_mode" ><label class="form-check-label" for="Dark_mode">Dark Mode</label></div></div>', (string) $field);
    }

    public function testBefore()
    {
        $field = Text::make('field')->onlyField()->before('<what></what>');

        $this->assertEquals('<div class="input-group"><what></what><input  class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testAfter()
    {
        $field = Text::make('field')->onlyField()->after('<what></what>');

        $this->assertEquals('<div class="input-group"><input  class="form-control" id="Field" name="field" type="text" value=""><what></what></div>', (string) $field);
    }

    public function testFileAccept()
    {
        $field = File::make('field')->onlyField()->accept(['image/*']);

        $this->assertStringContainsString('accept="image/*"', (string) $field);
    }

    public function testName()
    {
        $field = Text::make('field')->name('FooBar');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="FooBar">Foobar</label><input  class="form-control" id="FooBar" name="FooBar" type="text" value=""></div>', (string) $field);
    }

    public function testAttributes()
    {
        $field = Text::make('field')->attributes([
            'data-name' => 'superman',
            'data-set' => 'heroes',
        ]);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" data-name="superman" data-set="heroes" name="field" type="text" value=""></div>', (string) $field);
    }

    public function testPlaceholder()
    {
        $field = Text::make('field')->placeholder('Superman');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input  class="form-control" id="Field" placeholder="Superman" name="field" type="text" value=""></div>', (string) $field);
    }

    // model
}
