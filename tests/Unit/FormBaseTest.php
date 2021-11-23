<?php


namespace Tests\Unit;

use Tests\TestCase;
use Grafite\Forms\Forms\BaseForm;
use Illuminate\Support\Facades\Route;
use Grafite\Forms\Fields\Password;

class UserSecurityForm extends BaseForm
{
    public $route = 'user.security';

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

class FormBaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('user/security')->name('user.security');

        $this->form = app(UserSecurityForm::class);
    }

    public function testMake()
    {
        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/security', $form);
        $this->assertStringContainsString('method="POST"', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="form-label" for="Password">Password</label><input class="form-control" id="Password" name="password" type="password" value=""></div>', $form);
    }

    public function testMakeRenderedFields()
    {
        $form = $this->form->make()->renderedFields();

        $this->assertStringNotContainsString('http://localhost/user/security', $form);
        $this->assertStringNotContainsString('method="POST"', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="form-label" for="Password">Password</label><input class="form-control" id="Password" name="password" type="password" value=""></div>', $form);
    }
}
