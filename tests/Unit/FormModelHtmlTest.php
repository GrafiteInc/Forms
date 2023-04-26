<?php

namespace Tests\Unit;

use Tests\TestCase;
use Grafite\Forms\Fields\Name;
use Grafite\Forms\Fields\Email;
use Grafite\Forms\Traits\HasForm;
use Grafite\Forms\Forms\ModelForm;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;

class CarForm extends ModelForm
{
    public $model = Car::class;

    public $routePrefix = 'cars';

    public $modalTitle = 'Car Thingy';

    public $triggerContent = 'Design a new Car!';

    public $triggerClass = 'btn btn-obtuse';

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

class Car extends Model
{
    use HasForm;

    public $form = CarForm::class;
}

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

        Route::post('cars')->name('cars.store');
        Route::post('cars/search')->name('cars.search');
        Route::get('cars')->name('cars.index');
        Route::get('cars/{id}')->name('cars.edit');
        Route::put('cars/{id}')->name('cars.update');
        Route::delete('cars/{id}')->name('cars.destroy');

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

    public function testCarForm()
    {
        $form = (new Car)->form()->create();

        $this->assertStringContainsString('<label class="control-label" for="Name">Name</label>', $form);
    }

    public function testCarFormHasId()
    {
        $form = (new Car)->form()->create();

        $this->assertStringContainsString($form->getFormId(), $form->render());
        $this->assertNotNull($form->getFormId());
    }

    public function testCarFormAsModal()
    {
        $form = (new Car)->form()->create()->asModal();

        $this->assertStringContainsString('<h5 class="modal-title">Car Thingy</h5>', $form);
        $this->assertStringContainsString("btn btn-obtuse", $form);
        $this->assertStringContainsString("Design a new Car!", $form);
        $this->assertStringContainsString("data-toggle", $form);
        $this->assertStringContainsString("data-target", $form);
        $this->assertStringContainsString("data-bs-target", $form);
        $this->assertStringContainsString("data-bs-toggle", $form);
    }

    public function testCarFormAsModalWithCustomOptions()
    {
        $form = (new Car)->form()->create()->asModal('make new button now', 'btn w-100', 'this model of car is the best', 'New Car Builder Tool');

        $this->assertStringContainsString('<h5 class="modal-title">New Car Builder Tool</h5>', $form);
        $this->assertStringContainsString("btn w-100", $form);
        $this->assertStringContainsString("this model of car is the best", $form);
        $this->assertStringContainsString("make new button now", $form);
        $this->assertStringContainsString("data-toggle", $form);
        $this->assertStringContainsString("data-target", $form);
        $this->assertStringContainsString("data-bs-target", $form);
        $this->assertStringContainsString("data-bs-toggle", $form);
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
