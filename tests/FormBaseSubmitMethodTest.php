<?php

use Grafite\FormMaker\Fields\Password;
use Illuminate\Support\Facades\Route;
use Grafite\FormMaker\Forms\BaseForm;

class UserSecurityAjaxForm extends BaseForm
{
    public $route = 'user.security';

    public $submitMethod = 'ajax';

    public $buttons = [
        'submit' => 'Save'
    ];

    public function fields()
    {
        return [
            Password::make('password'),
        ];
    }
}

class FormBaseSubmitMethodTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('user/security')->name('user.security');

        $this->form = app(UserSecurityAjaxForm::class);
    }

    public function testMake()
    {
        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/security', $form);
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString("ajax(event)", $form);
        $this->assertStringContainsString('type="button"', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Password">Password</label><input  class="form-control" id="Password" name="password" type="password" value=""></div>', $form);
    }
}
