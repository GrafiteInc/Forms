<?php

use Grafite\Forms\Forms\Form;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Fields\Email;
use Illuminate\Support\Facades\Route;
use Grafite\Forms\Forms\ModelForm;

class FormHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);
    }

    public function testSingleField()
    {
        $field = form()->makeField(\Grafite\Forms\Fields\Text::class, 'name');

        $this->assertStringContainsString('type="text"', $field);
        $this->assertStringContainsString('name="name"', $field);
    }
}
