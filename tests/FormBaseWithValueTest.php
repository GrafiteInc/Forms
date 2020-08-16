<?php

use Grafite\Forms\Fields\Email;
use Grafite\Forms\Forms\BaseForm;
use Illuminate\Support\Facades\Route;
use Grafite\Forms\Fields\Password;

class UserWithValueForm extends BaseForm
{
    public $route = 'user.settings';

    public $buttons = [
        'submit' => 'Save'
    ];

    public function fields()
    {
        return [
            Email::make('email', [
                'value' => 'foobar@foo.com',
            ]),
        ];
    }
}

class FormBaseWithValueTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('user/settings')->name('user.settings');

        $this->form = app(UserWithValueForm::class);
    }

    public function testMake()
    {
        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/settings', $form);
        $this->assertStringContainsString('method="POST"', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Email">Email</label><input  class="form-control" id="Email" name="email" type="email" value="foobar@foo.com"></div>', $form);
    }
}