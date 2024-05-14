<?php

namespace Tests\Unit;

use Tests\TestCase;
use Grafite\Forms\Fields\Url;
use Grafite\Forms\Fields\Code;
use Grafite\Forms\Fields\Date;
use Grafite\Forms\Fields\File;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Fields\Time;
use Grafite\Forms\Fields\Week;
use Grafite\Forms\Fields\Color;
use Grafite\Forms\Fields\Email;
use Grafite\Forms\Fields\Field;
use Grafite\Forms\Fields\Image;
use Grafite\Forms\Fields\Month;
use Grafite\Forms\Fields\Radio;
use Grafite\Forms\Fields\Range;
use Grafite\Forms\Fields\HasOne;
use Grafite\Forms\Fields\Hidden;
use Grafite\Forms\Fields\Number;
use Grafite\Forms\Fields\Search;
use Grafite\Forms\Fields\Select;
use Grafite\Forms\Fields\Decimal;
use Grafite\Forms\Fields\HasMany;
use Grafite\Forms\Fields\Checkbox;
use Grafite\Forms\Fields\Datalist;
use Grafite\Forms\Fields\Dropzone;
use Grafite\Forms\Fields\Password;
use Grafite\Forms\Fields\TextArea;
use Grafite\Forms\Fields\Telephone;
use Grafite\Forms\Fields\Typeahead;
use Grafite\Forms\Fields\CustomFile;
use Grafite\Forms\Fields\RadioInline;
use Illuminate\Support\Facades\Route;
use Grafite\Forms\Fields\DatetimeLocal;
use Grafite\Forms\Fields\CheckboxInline;
use Grafite\Forms\Fields\Bootstrap\Select2;
use Grafite\Forms\Fields\PasswordWithReveal;
use Grafite\Forms\Fields\Bootstrap\SimpleSelect;

class FieldTest extends TestCase
{
    public function testText()
    {
        $field = Text::make('address', [
            'placeholder' => 'address'
        ])->toArray();

        $this->assertEquals('address', $field['attributes']['placeholder']);
    }

    public function testEmail()
    {
        $field = Email::make('address', [
            'placeholder' => 'address'
        ])->toArray();

        $this->assertEquals('address', $field['attributes']['placeholder']);
    }

    public function testCheckbox()
    {
        $field = Checkbox::make('wants_emails', [
            'placeholder' => 'wants_emails'
        ])->toArray();

        $this->assertEquals('wants_emails', $field['attributes']['placeholder']);
    }

    public function testCheckboxInline()
    {
        $field = CheckboxInline::make('field')->toArray();

        $this->assertEquals('', $field['attributes']['class']);
    }

    public function testCheckboxInlineAttributes()
    {
        $field = CheckboxInline::make('checkbox-field')
            ->required()
            ->placeholder('bam')
            ->toArray();

        $this->assertEquals('', $field['attributes']['class']);
        $this->assertEquals('bam', $field['attributes']['placeholder']);
    }

    public function testColor()
    {
        $field = Color::make('field')->toArray();

        $this->assertEquals('color', $field['type']);
    }

    public function testCode()
    {
        $field = Code::make('field')->option('theme', 'dark')->toArray();

        $this->assertEquals('dark', $field['options']['theme']);
    }

    public function testCustomFile()
    {
        $field = CustomFile::make('field')->toArray();

        $this->assertEquals('custom-file', $field['type']);
    }

    public function testDate()
    {
        $field = Date::make('field')->toArray();

        $this->assertEquals('date', $field['type']);
        $this->assertEquals('Y-m-d', $field['format']);
    }

    public function testDatetimeLocal()
    {
        $field = DatetimeLocal::make('field')->toArray();

        $this->assertEquals('datetime-local', $field['type']);
        $this->assertEquals('Y-m-d\TH:i', $field['format']);
    }

    public function testDecimal()
    {
        $field = Decimal::make('field')->toArray();

        $this->assertEquals('number', $field['type']);
    }

    public function testField()
    {
        $field = Field::make('field')->toArray();

        $this->assertEquals('text', $field['type']);
    }

    public function testFile()
    {
        $field = File::make('field')->toArray();

        $this->assertEquals('file', $field['type']);
    }

    public function testHasMany()
    {
        $field = HasMany::make('field')->toArray();

        $this->assertEquals('relationship', $field['type']);
    }

    public function testHasOne()
    {
        $field = HasOne::make('field')->toArray();

        $this->assertEquals('relationship', $field['type']);
    }

    public function testHidden()
    {
        $field = Hidden::make('field')->toArray();

        $this->assertEquals('hidden', $field['type']);
    }

    public function testImage()
    {
        $field = Image::make('field')->toArray();

        $this->assertEquals('image', $field['type']);
    }

    public function testMonth()
    {
        $field = Month::make('field')->toArray();

        $this->assertEquals('month', $field['type']);
    }

    public function testNumber()
    {
        $field = Number::make('field')->toArray();

        $this->assertEquals('number', $field['type']);
    }

    public function testPassword()
    {
        $field = Password::make('field')->toArray();

        $this->assertEquals('password', $field['type']);
    }

    public function testRadio()
    {
        $field = Radio::make('field')->toArray();

        $this->assertEquals('radio', $field['type']);
    }

    public function testRadioInline()
    {
        $field = RadioInline::make('field')->toArray();

        $this->assertEquals('radio', $field['type']);
        $this->assertEquals('form-check-input', $field['attributes']['class']);
    }

    public function testRange()
    {
        $field = Range::make('field')->toArray();

        $this->assertEquals('range', $field['type']);
    }

    public function testSelect()
    {
        $field = Select::make('field', [
            'options' => [
                'joe' => 'joe',
                'john' => 'john',
                'katie' => 'katie',
            ]
        ])->toArray();

        $this->assertEquals('select', $field['type']);
        $this->assertEquals('joe', $field['options']['joe']);
    }

    public function testSelect2()
    {
        $field = Select2::make('field', [
            'options' => [
                'joe' => 'joe',
                'john' => 'john',
                'katie' => 'katie',
            ]
        ])->searchable()->toArray();

        $this->assertStringContainsString('minimumResultsForSearch: _config.searchable ? 3 : Infinity,', $field['assets']['js']);
        $this->assertEquals('select', $field['type']);
        $this->assertEquals('joe', $field['options']['joe']);
    }

    public function testSimpleSelect()
    {
        $field = SimpleSelect::make('field', [
            'options' => [
                'joe' => 'joe',
                'john' => 'john',
                'katie' => 'katie',
            ]
        ])->searchable()->toArray();

        $this->assertStringContainsString('.bs-select', $field['assets']['styles']);
        $this->assertStringContainsString('_formsjs_bootstrapCustomSelectField', $field['assets']['js']);
        $this->assertEquals('select', $field['type']);
        $this->assertEquals('joe', $field['options']['joe']);
    }

    public function testSelectWithOptionsMethod()
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

    public function testTelephone()
    {
        $field = Telephone::make('field')->toArray();

        $this->assertEquals('tel', $field['type']);
    }

    public function testTextarea()
    {
        $field = TextArea::make('field')->toArray();

        $this->assertEquals('textarea', $field['type']);
    }

    public function testTime()
    {
        $field = Time::make('field')->toArray();

        $this->assertEquals('time', $field['type']);
    }

    public function testDropzone()
    {
        Route::post('user/history')->name('user.history');

        $field = Dropzone::make('field')->option('route', 'user.history')->option('theme', 'dark');

        $this->assertStringContainsString('dropzone-wrapper', (string) $field);
    }

    public function testUrl()
    {
        $field = Url::make('field')->toArray();

        $this->assertEquals('url', $field['type']);
    }

    public function testWeek()
    {
        $field = Week::make('field')->toArray();

        $this->assertEquals('week', $field['type']);
    }

    public function testDatalist()
    {
        $field = Datalist::make('field')->selectOptions([
            'Batman',
            'Superman',
            'Black Panther',
        ])->value('Batman');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input type="search" class="form-control" id="Field" value="Batman" name="field" list="Field-list"><datalist id="Field-list"><option value="Batman"><option value="Superman"><option value="Black Panther"></datalist></div>', (string) $field);
    }

    public function testSearch()
    {
        $field = Search::make('field')->value('Batman');

        $this->assertEquals('<div class="form-group"><label class="control-label" for="Field">Field</label><input class="form-control" id="Field" name="field" type="search" value="Batman"></div>', (string) $field);
    }

    public function testTypeahead()
    {
        $field = Typeahead::make('names', [
            'matches' => json_encode(["Alfred", "Jarvis"])
        ])->toArray();

        $this->assertEquals(json_encode(["Alfred", "Jarvis"]), $field['options']['matches']);
        $this->assertStringContainsString('typeahead__container', $field['template']);
    }

    public function testPasswordWithReveal()
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

    public function testPasswordWithRevealTemplate()
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
