<?php

namespace Tests\Unit;

use Grafite\Forms\Fields\Bootstrap\Select2;
use Grafite\Forms\Fields\Bootstrap\SimpleSelect;
use Grafite\Forms\Fields\Checkbox;
use Grafite\Forms\Fields\CheckboxInline;
use Grafite\Forms\Fields\Code;
use Grafite\Forms\Fields\Color;
use Grafite\Forms\Fields\CustomFile;
use Grafite\Forms\Fields\Datalist;
use Grafite\Forms\Fields\Date;
use Grafite\Forms\Fields\DatetimeLocal;
use Grafite\Forms\Fields\Decimal;
use Grafite\Forms\Fields\Dropzone;
use Grafite\Forms\Fields\Email;
use Grafite\Forms\Fields\Field;
use Grafite\Forms\Fields\File;
use Grafite\Forms\Fields\HasMany;
use Grafite\Forms\Fields\HasOne;
use Grafite\Forms\Fields\Hidden;
use Grafite\Forms\Fields\Image;
use Grafite\Forms\Fields\Month;
use Grafite\Forms\Fields\Number;
use Grafite\Forms\Fields\Password;
use Grafite\Forms\Fields\PasswordWithReveal;
use Grafite\Forms\Fields\Radio;
use Grafite\Forms\Fields\RadioInline;
use Grafite\Forms\Fields\Range;
use Grafite\Forms\Fields\Search;
use Grafite\Forms\Fields\Select;
use Grafite\Forms\Fields\Telephone;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Fields\TextArea;
use Grafite\Forms\Fields\Time;
use Grafite\Forms\Fields\Typeahead;
use Grafite\Forms\Fields\Url;
use Grafite\Forms\Fields\Week;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FieldTest extends TestCase
{
    public function test_text()
    {
        $field = Text::make('address', [
            'placeholder' => 'address',
        ])->toArray();

        $this->assertEquals('address', $field['attributes']['placeholder']);
    }

    public function test_email()
    {
        $field = Email::make('address', [
            'placeholder' => 'address',
        ])->toArray();

        $this->assertEquals('address', $field['attributes']['placeholder']);
    }

    public function test_checkbox()
    {
        $field = Checkbox::make('wants_emails', [
            'placeholder' => 'wants_emails',
        ])->toArray();

        $this->assertEquals('wants_emails', $field['attributes']['placeholder']);
    }

    public function test_checkbox_inline()
    {
        $field = CheckboxInline::make('field')->toArray();

        $this->assertEquals('', $field['attributes']['class']);
    }

    public function test_checkbox_inline_attributes()
    {
        $field = CheckboxInline::make('checkbox-field')
            ->required()
            ->placeholder('bam')
            ->toArray();

        $this->assertEquals('', $field['attributes']['class']);
        $this->assertEquals('bam', $field['attributes']['placeholder']);
    }

    public function test_color()
    {
        $field = Color::make('field')->toArray();

        $this->assertEquals('color', $field['type']);
    }

    public function test_code()
    {
        $field = Code::make('field')->option('theme', 'dark')->toArray();

        $this->assertEquals('dark', $field['options']['theme']);
    }

    public function test_custom_file()
    {
        $field = CustomFile::make('field')->toArray();

        $this->assertEquals('custom-file', $field['type']);
    }

    public function test_date()
    {
        $field = Date::make('field')->toArray();

        $this->assertEquals('date', $field['type']);
        $this->assertEquals('Y-m-d', $field['format']);
    }

    public function test_datetime_local()
    {
        $field = DatetimeLocal::make('field')->toArray();

        $this->assertEquals('datetime-local', $field['type']);
        $this->assertEquals('Y-m-d\TH:i', $field['format']);
    }

    public function test_decimal()
    {
        $field = Decimal::make('field')->toArray();

        $this->assertEquals('number', $field['type']);
    }

    public function test_field()
    {
        $field = Field::make('field')->toArray();

        $this->assertEquals('text', $field['type']);
    }

    public function test_file()
    {
        $field = File::make('field')->toArray();

        $this->assertEquals('file', $field['type']);
    }

    public function test_has_many()
    {
        $field = HasMany::make('field')->toArray();

        $this->assertEquals('relationship', $field['type']);
    }

    public function test_has_one()
    {
        $field = HasOne::make('field')->toArray();

        $this->assertEquals('relationship', $field['type']);
    }

    public function test_hidden()
    {
        $field = Hidden::make('field')->toArray();

        $this->assertEquals('hidden', $field['type']);
    }

    public function test_image()
    {
        $field = Image::make('field')->toArray();

        $this->assertEquals('image', $field['type']);
    }

    public function test_month()
    {
        $field = Month::make('field')->toArray();

        $this->assertEquals('month', $field['type']);
    }

    public function test_number()
    {
        $field = Number::make('field')->toArray();

        $this->assertEquals('number', $field['type']);
    }

    public function test_password()
    {
        $field = Password::make('field')->toArray();

        $this->assertEquals('password', $field['type']);
    }

    public function test_radio()
    {
        $field = Radio::make('field')->toArray();

        $this->assertEquals('radio', $field['type']);
    }

    public function test_radio_inline()
    {
        $field = RadioInline::make('field')->toArray();

        $this->assertEquals('radio', $field['type']);
        $this->assertEquals('form-check-input', $field['attributes']['class']);
    }

    public function test_range()
    {
        $field = Range::make('field')->toArray();

        $this->assertEquals('range', $field['type']);
    }

    public function test_select()
    {
        $field = Select::make('field', [
            'options' => [
                'joe' => 'joe',
                'john' => 'john',
                'katie' => 'katie',
            ],
        ])->toArray();

        $this->assertEquals('select', $field['type']);
        $this->assertEquals('joe', $field['options']['joe']);
    }

    public function test_select2()
    {
        $field = Select2::make('field', [
            'options' => [
                'joe' => 'joe',
                'john' => 'john',
                'katie' => 'katie',
            ],
        ])->searchable()->toArray();

        $this->assertStringContainsString('minimumResultsForSearch: _config.searchable ? 3 : Infinity,', $field['assets']['js']);
        $this->assertEquals('select', $field['type']);
        $this->assertEquals('joe', $field['options']['joe']);
    }

    public function test_simple_select()
    {
        $field = SimpleSelect::make('field', [
            'options' => [
                'joe' => 'joe',
                'john' => 'john',
                'katie' => 'katie',
            ],
        ])->searchable()->toArray();

        $this->assertStringContainsString('.bs-select', $field['assets']['styles']);
        $this->assertStringContainsString('_formsjs_bootstrapCustomSelectField', $field['assets']['js']);
        $this->assertEquals('select', $field['type']);
        $this->assertEquals('joe', $field['options']['joe']);
    }

    public function test_select_with_options_method()
    {
        $field = Select::make('field')
            ->multiple()
            ->options([
                'joe' => 'joe',
                'john' => 'john',
                'katie' => 'katie',
            ])->toArray();

        $this->assertEquals('select', $field['type']);
        $this->assertEquals('joe', $field['options']['joe']);
        $this->assertTrue($field['attributes']['multiple']);
    }

    public function test_telephone()
    {
        $field = Telephone::make('field')->toArray();

        $this->assertEquals('tel', $field['type']);
    }

    public function test_textarea()
    {
        $field = TextArea::make('field')->toArray();

        $this->assertEquals('textarea', $field['type']);
    }

    public function test_time()
    {
        $field = Time::make('field')->toArray();

        $this->assertEquals('time', $field['type']);
    }

    public function test_dropzone()
    {
        Route::post('user/history')->name('user.history');

        $field = Dropzone::make('field')->option('route', 'user.history')->option('theme', 'dark');

        $this->assertStringContainsString('dropzone-wrapper', (string) $field);
    }

    public function test_url()
    {
        $field = Url::make('field')->toArray();

        $this->assertEquals('url', $field['type']);
    }

    public function test_week()
    {
        $field = Week::make('field')->toArray();

        $this->assertEquals('week', $field['type']);
    }

    public function test_datalist()
    {
        $field = Datalist::make('field')->selectOptions([
            'Batman',
            'Superman',
            'Black Panther',
        ])->value('Batman');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input type="search" class="form-control" id="Field" value="Batman" name="field" list="Field-list"><datalist id="Field-list"><option value="Batman"><option value="Superman"><option value="Black Panther"></datalist></div>', (string) $field);
    }

    public function test_search()
    {
        $field = Search::make('field')->value('Batman');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" name="field" type="search" value="Batman"></div>', (string) $field);
    }

    public function test_typeahead()
    {
        $field = Typeahead::make('names', [
            'matches' => json_encode(['Alfred', 'Jarvis']),
        ])->toArray();

        $this->assertEquals(json_encode(['Alfred', 'Jarvis']), $field['options']['matches']);
        $this->assertStringContainsString('typeahead__container', $field['template']);
    }

    public function test_password_with_reveal()
    {
        $field = PasswordWithReveal::make('names', [
            'toggle-classes' => 'foo-bar-biz',
        ])
            ->option('toggle-selector', 'pwYoYo')
            ->option('toggle', 'fooBar')
            ->toArray();

        $this->assertContains('toggle-classes', array_keys($field['options']));
        $this->assertContains('toggle-selector', array_keys($field['options']));
        $this->assertContains('toggle', array_keys($field['options']));
    }

    public function test_password_with_reveal_template()
    {
        $field = PasswordWithReveal::make('password', [
            'toggle-classes' => 'foo-bar-biz',
        ])
            ->value('wtf')
            ->attribute('data-bad', 'good')
            ->option('toggle-selector', 'pwYoYo')
            ->option('toggle', 'fooBar');

        $this->assertStringContainsString('<input class="form-control" id="Password" data-bad="good" data-formsjs-onload-data="pwYoYo" data-formsjs-onload="_formsjs_passwordWithRevealField" name="password" type="password" value="">', (string) $field);
        $this->assertStringContainsString('<button tabindex="-1" type="button" class="pwYoYo-Password foo-bar-biz">fooBar</button>', (string) $field);
    }
}
