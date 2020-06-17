<?php

use Grafite\FormMaker\Fields\Name;
use Grafite\FormMaker\Fields\Email;
use Grafite\FormMaker\Fields\Quill;
use Illuminate\Support\Facades\Route;
use Grafite\FormMaker\Forms\ModelForm;

class UserForm extends ModelForm
{
    public $model = User::class;

    public $routePrefix = 'users';

    public $formId = 'userForm';

    public $buttons = [
        'submit' => 'Save'
    ];

    public $buttonClasses = [
        'delete' => 'deleter-button',
        'submit' => 'submit-button',
    ];

    public $formClass = 'formy-informer';
    public $formDeleteClass = 'formy-deleter';

    public function fields()
    {
        return [
            Name::make('name', [
                'value' => $this->getSpecialName()
            ]),
            Email::make('email'),
            Quill::make('history')
        ];
    }

    public function getSpecialName()
    {
        if ($this->hasInstance()) {
            return $this->instance->name.' - jedi master';
        }

        return null;
    }
}

class FormModelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('users')->name('users.store');
        Route::get('users')->name('users.index');
        Route::put('users/{id}')->name('users.update');
        Route::delete('users/{id}')->name('users.destroy');

        $this->form = app(UserForm::class);
    }

    public function testCreate()
    {
        $form = $this->form->create();

        $this->assertStringContainsString('http://localhost/users', $form);
        $this->assertStringContainsString('method="POST"', $form);

        $this->assertStringContainsString('class="formy-informer"', $form);
        $this->assertStringContainsString('id="userForm"', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Name">Name</label><input  class="form-control" id="Name" name="name" type="text" value=""></div>', $form);
        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Email">Email</label><input  class="form-control" id="Email" name="email" type="email" value=""></div>', $form);
    }

    public function testUpdate()
    {
        $user = new User();

        $form = $this->form->edit($user);

        $this->assertStringContainsString('http://localhost/users/3', $form);
        $this->assertStringContainsString('PUT', $form);
        $this->assertStringContainsString('class="submit-button', $form);
    }

    public function testUpdateIsDisabled()
    {
        $user = new User();

        $form = $this->form->disable()->edit($user);

        $this->assertStringContainsString('http://localhost/users/3', $form);
        $this->assertStringContainsString('PUT', $form);
        $this->assertStringContainsString('disabled="disabled"', $form);
        $this->assertStringNotContainsString('class="submit-button', $form);
    }

    public function testUpdateIsDisabledWhen()
    {
        $user = new User();

        $form = $this->form->disabledWhen(function () {
            return true;
        })->edit($user);

        $this->assertStringContainsString('http://localhost/users/3', $form);
        $this->assertStringContainsString('PUT', $form);
        $this->assertStringContainsString('disabled="disabled"', $form);
        $this->assertStringNotContainsString('class="submit-button', $form);
    }

    public function testDelete()
    {
        $user = new User();

        $form = $this->form->delete($user);

        $this->assertStringContainsString('class="formy-deleter"', $form);

        $this->assertStringContainsString('http://localhost/users/3', $form);
        $this->assertStringContainsString('DELETE', $form);
        $this->assertStringContainsString('class="deleter-button', $form);
    }

    public function testRenderedFieldsForEdit()
    {
        $user = new User();

        $form = $this->form->edit($user)->renderedFields();

        $this->assertStringNotContainsString('http://localhost/users/3', $form);
        $this->assertStringNotContainsString('PUT', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Email">Email</label><input  class="form-control" id="Email" name="email" type="email" value=""></div>', $form);
    }

    public function testRenderedFieldsForCreate()
    {
        $form = $this->form->create()->renderedFields();

        $this->assertStringNotContainsString('http://localhost/users', $form);
        $this->assertStringNotContainsString('POST', $form);

        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Email">Email</label><input  class="form-control" id="Email" name="email" type="email" value=""></div>', $form);
    }
}