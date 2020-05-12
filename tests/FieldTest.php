<?php

use Grafite\FormMaker\Fields\Url;
use Grafite\FormMaker\Fields\Date;
use Grafite\FormMaker\Fields\File;
use Grafite\FormMaker\Fields\Text;
use Grafite\FormMaker\Fields\Time;
use Grafite\FormMaker\Fields\Week;
use Grafite\FormMaker\Fields\Color;
use Grafite\FormMaker\Fields\Email;
use Grafite\FormMaker\Fields\Field;
use Grafite\FormMaker\Fields\Image;
use Grafite\FormMaker\Fields\Month;
use Grafite\FormMaker\Fields\Radio;
use Grafite\FormMaker\Fields\Range;
use Grafite\FormMaker\Fields\HasOne;
use Grafite\FormMaker\Fields\Hidden;
use Grafite\FormMaker\Fields\Number;
use Grafite\FormMaker\Fields\Select;
use Grafite\FormMaker\Fields\Decimal;
use Grafite\FormMaker\Fields\HasMany;
use Grafite\FormMaker\Fields\Checkbox;
use Grafite\FormMaker\Fields\Password;
use Grafite\FormMaker\Fields\TextArea;
use Grafite\FormMaker\Fields\Telephone;
use Grafite\FormMaker\Fields\Typeahead;
use Grafite\FormMaker\Fields\CustomFile;
use Grafite\FormMaker\Fields\RadioInline;
use Grafite\FormMaker\Fields\DatetimeLocal;
use Grafite\FormMaker\Fields\CheckboxInline;

class FieldTest extends TestCase
{
    public function testText()
    {
        $field = Text::make('address', [
            'placeholder' => 'address'
        ]);

        $this->assertEquals('address', array_key_first($field));
        $this->assertEquals('address', $field['address']['attributes']['placeholder']);
    }

    public function testEmail()
    {
        $field = Email::make('address', [
            'placeholder' => 'address'
        ]);

        $this->assertEquals('address', array_key_first($field));
        $this->assertEquals('address', $field['address']['attributes']['placeholder']);
    }

    public function testCheckbox()
    {
        $field = Checkbox::make('wants_emails', [
            'placeholder' => 'wants_emails'
        ]);

        $this->assertEquals('wants_emails', array_key_first($field));
        $this->assertEquals('wants_emails', $field['wants_emails']['attributes']['placeholder']);
    }

    public function testCheckboxInline()
    {
        $field = CheckboxInline::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('form-check-input', $field['field']['attributes']['class']);
    }

    public function testColor()
    {
        $field = Color::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('color', $field['field']['type']);
    }

    public function testCustomFile()
    {
        $field = CustomFile::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('custom-file', $field['field']['type']);
    }

    public function testDate()
    {
        $field = Date::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('date', $field['field']['type']);
        $this->assertEquals('Y-m-d', $field['field']['format']);
    }

    public function testDatetimeLocal()
    {
        $field = DatetimeLocal::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('datetime-local', $field['field']['type']);
        $this->assertEquals('Y-m-d\TH:i', $field['field']['format']);
    }

    public function testDecimal()
    {
        $field = Decimal::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('number', $field['field']['type']);
    }

    public function testField()
    {
        $field = Field::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('text', $field['field']['type']);
    }

    public function testFile()
    {
        $field = File::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('file', $field['field']['type']);
    }

    public function testHasMany()
    {
        $field = HasMany::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('relationship', $field['field']['type']);
    }

    public function testHasOne()
    {
        $field = HasOne::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('relationship', $field['field']['type']);
    }

    public function testHidden()
    {
        $field = Hidden::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('hidden', $field['field']['type']);
    }

    public function testImage()
    {
        $field = Image::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('image', $field['field']['type']);
    }

    public function testMonth()
    {
        $field = Month::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('month', $field['field']['type']);
    }

    public function testNumber()
    {
        $field = Number::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('number', $field['field']['type']);
    }

    public function testPassword()
    {
        $field = Password::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('password', $field['field']['type']);
    }

    public function testRadio()
    {
        $field = Radio::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('radio', $field['field']['type']);
    }

    public function testRadioInline()
    {
        $field = RadioInline::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('radio', $field['field']['type']);
        $this->assertEquals('form-check-input', $field['field']['attributes']['class']);
    }

    public function testRange()
    {
        $field = Range::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('range', $field['field']['type']);
    }

    public function testSelect()
    {
        $field = Select::make('field', [
            'options' => [
                'joe' => 'joe',
                'john' => 'john',
                'katie' => 'katie',
            ]
        ]);

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('select', $field['field']['type']);
        $this->assertEquals('joe', $field['field']['options']['joe']);
    }

    public function testTelephone()
    {
        $field = Telephone::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('tel', $field['field']['type']);
    }

    public function testTextarea()
    {
        $field = TextArea::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('textarea', $field['field']['type']);
    }

    public function testTime()
    {
        $field = Time::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('time', $field['field']['type']);
    }

    public function testUrl()
    {
        $field = Url::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('url', $field['field']['type']);
    }

    public function testWeek()
    {
        $field = Week::make('field');

        $this->assertEquals('field', array_key_first($field));
        $this->assertEquals('week', $field['field']['type']);
    }

    public function testTypeahead()
    {
        $field = Typeahead::make('names', [
            'matches' => json_encode(["Alfred", "Jarvis"])
        ]);

        $this->assertEquals('names', array_key_first($field));
        $this->assertEquals(json_encode(["Alfred", "Jarvis"]), $field['names']['attributes']['matches']);
        $this->assertStringContainsString('typeahead__container', $field['names']['template']);
    }
}