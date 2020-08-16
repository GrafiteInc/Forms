<?php

use Grafite\Forms\Fields\Name;
use Grafite\Forms\Fields\Email;
use Grafite\Forms\Fields\Quill;
use Illuminate\Support\Facades\Route;
use Grafite\Forms\Forms\ModelForm;

class UserExceptionForm extends ModelForm
{
    public $model = User::class;

    public $routePrefix = 'users';

    public $formId = 'userForm';

    public $buttons = [
        'submit' => 'Save',
        'cancel' => 'Cancel'
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
            Quill::make('history', [
                'toolbars' => [
                    'basic',
                    'image'
                ]
            ])
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

class FormModelExceptionTest extends TestCase
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

        $this->form = app(UserExceptionForm::class);
    }

    public function testNoImageRoute()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("You need to set an `upload_route` for handling image uploads to Quill.");

        $this->form->create();
    }
}