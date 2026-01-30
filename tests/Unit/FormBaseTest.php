<?php

namespace Tests\Unit;

use Grafite\Forms\Fields\Password;
use Grafite\Forms\Forms\BaseForm;
use Grafite\Forms\Html\Button;
use Grafite\Forms\Html\Link;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserSecurityForm extends BaseForm
{
    public $buttonsJustified = true;

    public $route = 'user.security';

    public function buttons()
    {
        return [
            Link::make('Cancel')->attributes(['href' => 'go-back']),
            Button::make('Hacker')->attributes(['onclick' => 'event.preventDefault();']),
            Button::make('Save')->cssClass('btn btn-secondary')->attributes(['submit' => 'submit']),
        ];
    }

    public function fields()
    {
        return [
            Password::make('password'),
        ];
    }
}

class FormBaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('user/security')->name('user.security');

        $this->form = app(UserSecurityForm::class);
    }

    public function test_make()
    {
        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/security', $form);
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString('justify-content-between', $form);
        $this->assertStringContainsString('preventDefault()', $form);
        $this->assertStringContainsString('Save', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Password">Password</label><input class="form-control" id="Password" name="password" type="password" value=""></div>', $form);
    }

    public function test_make_rendered_fields()
    {
        $form = $this->form->make()->renderedFields();

        $this->assertStringNotContainsString('http://localhost/user/security', $form);
        $this->assertStringNotContainsString('method="POST"', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Password">Password</label><input class="form-control" id="Password" name="password" type="password" value=""></div>', $form);
    }
}
