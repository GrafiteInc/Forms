<?php

use Grafite\Forms\Fields\Name;
use Grafite\Forms\Fields\Email;
use Illuminate\Support\Facades\Route;
use Grafite\Forms\Forms\ModelForm;
use Illuminate\Foundation\Auth\User;

class UserFormWithHtml extends ModelForm
{
    public $model = User::class;

    public $routePrefix = 'users';

    public $paginate = 1;

    public $formId = 'userForm';

    public $confirmMessage = 'Are you sure you want to delete this?';

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
                'visible' => true,
                'sortable' => true,
            ]),
            Email::make('email')
        ];
    }
}

class FormModelHtmlTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('users')->name('users.store');
        Route::post('users/search')->name('users.search');
        Route::get('users')->name('users.index');
        Route::get('users/{id}')->name('users.edit');
        Route::put('users/{id}')->name('users.update');
        Route::delete('users/{id}')->name('users.destroy');

        $user = new User;
        $user->name = 'Batman';
        $user->email = 'bruce@wayne.com';
        $user->password = bcrypt('password');
        $user->save();

        $user = new User;
        $user->name = 'Superman';
        $user->email = 'clark@dailybugle.com';
        $user->password = bcrypt('password');
        $user->save();

        $this->form = app(UserFormWithHtml::class);
    }

    public function testIndex()
    {
        $form = $this->form->index();

        $this->assertStringContainsString('http://localhost?sort_by=name&order=desc', $form);
        $this->assertStringContainsString('<th>Email</th>', $form);
        $this->assertStringContainsString('Batman', $form);
        $this->assertStringContainsString('confirm', $form);
        $this->assertStringContainsString('Are you sure you want to delete', $form);
        $this->assertStringContainsString('<a class="btn btn-outline-primary" href="http://localhost/users/1">Edit</a>', $form);
        $this->assertStringContainsString('http://localhost?page=2', $form);
    }

    public function testIndexToJson()
    {
        $form = $this->form->index()->toJson();

        $this->assertStringContainsString('current_page', $form);
        $this->assertStringContainsString('"email":"bruce@wayne.com"', $form);
        $this->assertStringContainsString('Batman', $form);
        $this->assertStringContainsString('page=2', $form);
    }

    public function testIndexSearchForm()
    {
        $form = $this->form->index()->search('users.search', 'Search Me', 'Search Waht?', 'get');

        $this->assertStringContainsString('GET', $form);
        $this->assertStringContainsString('Search Me', $form);
        $this->assertStringContainsString('Search Waht?', $form);
        $this->assertStringContainsString('users/search', $form);
        $this->assertStringContainsString('input', $form);
        $this->assertStringContainsString('button', $form);
    }
}