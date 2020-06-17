<?php

use Grafite\FormMaker\Fields\Text;
use Grafite\FormMaker\Forms\BaseForm;
use Illuminate\Support\Facades\Route;
use Grafite\FormMaker\Fields\Checkbox;

class UserSampleForm extends BaseForm
{
    public $route = 'user.sample';

    public $buttons = [
        'submit' => 'Save <span class="fas fa-save"></span>'
    ];

    public $buttonClasses = [
        'submit' => 'superman'
    ];

    public $formClass = 'batman';

    public function fields()
    {
        return [
            Text::make('name'),
        ];
    }
}

class UserSampleCreateForm extends BaseForm
{
    public $route = 'user.sample';

    public $buttons = [
        'submit' => 'Save'
    ];

    public $buttonClasses = [
        'submit' => 'superman',
    ];

    public $formClass = 'batman-horizontal';

    public $orientation = 'horizontal';

    public function fields()
    {
        return [
            Text::make('name'),
            Checkbox::make('is_cool', [
                'class' => 'batman-style'
            ])
        ];
    }
}

class FormExtensiveBaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('user/sample')->name('user.sample');

        $this->form = app(UserSampleForm::class);
    }

    public function testMake()
    {
        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/sample', $form);
        $this->assertStringContainsString('method="POST"', $form);

        $this->assertStringContainsString('<form method="POST" action="http://localhost/user/sample" accept-charset="UTF-8" class="batman">', $form);
        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Name">Name</label>', $form);
        $this->assertStringContainsString('<input  class="form-control" id="Name" name="name" type="text" value=""></div>', $form);
        $this->assertStringContainsString('<div class="col-md-12 d-flex justify-content-end"><button class="superman" type="submit">Save <span class="fas fa-save"></span></button></div></div></form>', $form);
    }

    public function testMakeWithClassChanges()
    {
        $form = app(UserSampleCreateForm::class)->make();

        $this->assertStringContainsString('http://localhost/user/sample', $form);
        $this->assertStringContainsString('method="POST"', $form);

        $this->assertStringContainsString('<form method="POST" action="http://localhost/user/sample" accept-charset="UTF-8" class="batman-horizontal">', $form);
        $this->assertStringContainsString('<div class="form-group row"><label class="col-md-2 col-form-label pt-0" for="Name">Name</label>', $form);
        $this->assertStringContainsString('<div class="col-md-10"><input  class="form-control" id="Name" name="name" type="text" value="">', $form);
        $this->assertStringContainsString('<legend class="col-md-2 col-form-label pt-0"></legend><div class="col-md-10"><div class="form-check">', $form);
        $this->assertStringContainsString('<input  class="form-check-input" id="Is_cool" type="checkbox" name="is_cool" ><label class="form-check-label" for="Is_cool">Is Cool</label>', $form);
    }
}
