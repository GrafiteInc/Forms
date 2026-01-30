<?php

namespace Tests\Unit;

use Grafite\Forms\Fields\Checkbox;
use Grafite\Forms\Fields\File;
use Grafite\Forms\Fields\Select;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Services\FormAssets;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FieldConfigProcessorTest extends TestCase
{
    public function test_label()
    {
        $field = Text::make('field')->label('FooBar');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">FooBar</label><input class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_value()
    {
        $field = Text::make('field')->value('FooBar');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" name="field" type="text" value="FooBar"></div>', (string) $field);
    }

    public function test_required()
    {
        $field = Text::make('field')->required();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" required name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_data()
    {
        $field = Text::make('field')->data('user', 123)->data('draggable', 'true');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" data-user="123" data-draggable="true" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_title()
    {
        $field = Text::make('field')->title('user-field');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" title="user-field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_style()
    {
        $field = Text::make('field')->style('color: #f00;');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" style="color: #f00;" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_id()
    {
        $field = Text::make('field')->id('superman');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="superman">Field</label><input class="form-control" id="superman" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_autocomplete()
    {
        $field = Text::make('field')->autocomplete();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" autocomplete name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_autofocus()
    {
        $field = Text::make('field')->autofocus();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" autofocus name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_multiple()
    {
        $field = Text::make('field')->multiple();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" multiple name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_step()
    {
        $field = Text::make('field')->step(5);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" step="5" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_pattern()
    {
        $field = Text::make('field')->pattern('^[A-Z0-9+_.-]+@[A-Z0-9.-]+$');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" pattern="^[A-Z0-9+_.-]+@[A-Z0-9.-]+$" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_max()
    {
        $field = Text::make('field')->max(4);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" max="4" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_min()
    {
        $field = Text::make('field')->min(4);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" min="4" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_maxlength()
    {
        $field = Text::make('field')->maxlength(4);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" maxlength="4" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_size()
    {
        $field = Text::make('field')->size(4);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" size="4" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_disabled()
    {
        $field = Text::make('field')->disabled();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" disabled name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_readonly()
    {
        $field = Text::make('field')->readonly();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" readonly name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_template()
    {
        $field = Text::make('field')->template('{label}{field}');

        $this->assertEquals('<label class="control-label" for="Field">Field</label><input class="form-control" id="Field" name="field" type="text" value="">', (string) $field);
    }

    public function test_view()
    {
        $field = Text::make('field')->view('test-view');

        $this->assertEquals('<div><label class="control-label" for="Field">Field</label></div>
<div><input class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_css_class()
    {
        $field = Text::make('field')->cssClass('foo-bar-baz');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="foo-bar-baz" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_label_class()
    {
        $field = Text::make('field')->labelClass('foo-bar-baz');

        $this->assertEquals('<div class="form-group"><label class="foo-bar-baz" for="Field">Field</label><input class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_table_class()
    {
        $field = Text::make('field')->tableClass('foo-bar-baz')->toArray();

        $this->assertEquals('foo-bar-baz', $field['table_class']);
    }

    public function test_hidden()
    {
        $field = Text::make('field')->hidden()->toArray();

        $this->assertEquals(false, $field['visible']);
    }

    public function test_visible()
    {
        $field = Text::make('field')->visible()->toArray();

        $this->assertEquals(true, $field['visible']);
    }

    public function test_null_value()
    {
        $field = Select::make('field')->selectOptions([
            'Superman' => 1,
            'Batman' => 2,
            'Ninja' => 3,
        ])->canSelectNone();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><select class="form-select" id="Field" name="field"><option value="" selected>None</option><option value="1">Superman</option><option value="2">Batman</option><option value="3">Ninja</option></select></div>', (string) $field);
    }

    public function test_submit_on_change()
    {
        $field = Select::make('field')->selectOptions([
            'Superman' => 1,
            'Batman' => 2,
            'Ninja' => 3,
        ])->canSelectNone()->submitOnChange();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><select class="form-select" id="Field" data-formsjs-onchange="FormsJS_submit" name="field"><option value="" selected>None</option><option value="1">Superman</option><option value="2">Batman</option><option value="3">Ninja</option></select></div>', (string) $field);
    }

    public function test_submit_on_key_up()
    {
        $field = Select::make('field')->selectOptions([
            'Superman' => 1,
            'Batman' => 2,
            'Ninja' => 3,
        ])->canSelectNone()->submitOnKeyUp();

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><select class="form-select" id="Field" data-formsjs-onkeyup="FormsJS_submit" name="field"><option value="" selected>None</option><option value="1">Superman</option><option value="2">Batman</option><option value="3">Ninja</option></select></div>', (string) $field);
    }

    public function test_hidden_unless()
    {
        $field = Select::make('field')->selectOptions([
            'Superman' => 1,
            'Batman' => 2,
            'Ninja' => 3,
        ])->canSelectNone()->hiddenUnless('Previous_Field', 'visible');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><select class="form-select" id="Field" name="field"><option value="" selected>None</option><option value="1">Superman</option><option value="2">Batman</option><option value="3">Ninja</option></select></div>', (string) $field);

        $assets = app(FormAssets::class)->render();

        $this->assertStringContainsString("document.getElementById('Previous_Field').addEventListener('change', _visual_validation_Previous_Field());", $assets);
    }

    public function test_null_label()
    {
        $field = Select::make('field')->selectOptions([
            'Superman' => 1,
            'Batman' => 2,
            'Ninja' => 3,
        ])->canSelectNone()->noneLabel('Nada');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><select class="form-select" id="Field" name="field"><option value="" selected>Nada</option><option value="1">Superman</option><option value="2">Batman</option><option value="3">Ninja</option></select></div>', (string) $field);
    }

    public function test_sortable()
    {
        $field = Text::make('field')->sortable()->toArray();

        $this->assertEquals(true, $field['sortable']);
    }

    public function test_wrapper()
    {
        $field = Text::make('field')->groupClass('foo-bar');

        $this->assertEquals('<div class="foo-bar"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_without_wrapper()
    {
        $field = Text::make('field')->ungrouped();

        $this->assertEquals('<label class="control-label" for="Field">Field</label><input class="form-control" id="Field" name="field" type="text" value="">', (string) $field);
    }

    public function test_without_label()
    {
        $field = Text::make('field')->ungrouped()->withoutLabel();

        $this->assertEquals('<input class="form-control" id="Field" name="field" type="text" value="">', (string) $field);
    }

    public function test_only_field()
    {
        $field = Text::make('field')->onlyField();

        $this->assertEquals('<input class="form-control" id="Field" name="field" type="text" value="">', (string) $field);
    }

    public function test_legend()
    {
        $field = Checkbox::make('dark_mode')->legend('Dark Mode');

        $this->assertEquals('<div class="form-group"><div class="form-check"><input class="form-check-input" id="Dark_mode" type="checkbox" name="dark_mode"><label class="form-check-label" for="Dark_mode">Dark Mode</label></div></div>', (string) $field);
    }

    public function test_before()
    {
        $field = Text::make('field')->onlyField()->before('<what></what>');

        $this->assertEquals('<div class="input-group"><what></what><input class="form-control" id="Field" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_after()
    {
        $field = Text::make('field')->onlyField()->after('<what></what>');

        $this->assertEquals('<div class="input-group"><input class="form-control" id="Field" name="field" type="text" value=""><what></what></div>', (string) $field);
    }

    public function test_file_accept()
    {
        $field = File::make('field')->onlyField()->accept(['image/*']);

        $this->assertStringContainsString('accept="image/*"', (string) $field);
    }

    public function test_name()
    {
        $field = Text::make('field')->name('FooBar');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="FooBar">Foobar</label><input class="form-control" id="FooBar" name="FooBar" type="text" value=""></div>', (string) $field);
    }

    public function test_attributes()
    {
        $field = Text::make('field')->attributes([
            'data-name' => 'superman',
            'data-set' => 'heroes',
        ]);

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" data-name="superman" data-set="heroes" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_placeholder()
    {
        $field = Text::make('field')->placeholder('Superman');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" placeholder="Superman" name="field" type="text" value=""></div>', (string) $field);
    }

    public function test_model()
    {
        $user = new User;
        $user->name = 'Bruce';
        $user->email = 'batman@wayneenterprises.com';
        $user->password = Hash::make('beatTheJoker');

        $user->save();

        $field = Text::make('name')->instance(User::find(1));

        $this->assertStringContainsString('Bruce', (string) $field);
    }
}
