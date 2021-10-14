<?php

use Grafite\Forms\Fields\Url;
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
use Grafite\Forms\Fields\Select;
use Grafite\Forms\Fields\Decimal;
use Grafite\Forms\Fields\HasMany;
use Grafite\Forms\Fields\Checkbox;
use Grafite\Forms\Fields\Password;
use Grafite\Forms\Fields\TextArea;
use Grafite\Forms\Fields\Telephone;
use Grafite\Forms\Fields\Typeahead;
use Grafite\Forms\Fields\CustomFile;
use Grafite\Forms\Fields\RadioInline;
use Grafite\Forms\Fields\DatetimeLocal;
use Grafite\Forms\Fields\CheckboxInline;

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

    public function testTypeahead()
    {
        $field = Typeahead::make('names', [
            'matches' => json_encode(["Alfred", "Jarvis"])
        ])->toArray();

        $this->assertEquals(json_encode(["Alfred", "Jarvis"]), $field['attributes']['matches']);
        $this->assertStringContainsString('typeahead__container', $field['template']);
    }
}
