<?php

namespace Tests\Unit;

use Grafite\Forms\Fields\Checkbox;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Forms\BaseForm;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserSampleForm extends BaseForm
{
    public $formId = 'superman';

    public $route = 'user.sample';

    public $buttons = [
        'submit' => 'Save <span class="fas fa-save"></span>',
    ];

    public $buttonClasses = [
        'submit' => 'superman',
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
    public $formId = 'batman';

    public $route = 'user.sample';

    public $buttons = [
        'submit' => 'Save',
    ];

    public $buttonClasses = [
        'submit' => 'superman',
    ];

    public $formClass = 'batman-horizontal';

    public $orientation = 'horizontal';

    public function setUp()
    {
        $this->buttons['submit'] = 'DO NOT CLICK!';
    }

    public function fields()
    {
        return [
            Text::make('name'),
            Checkbox::make('is_cool', [
                'class' => 'batman-style',
            ]),
        ];
    }
}

class FormExtensiveBaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('user/sample')->name('user.sample');

        $this->form = app(UserSampleForm::class);
    }

    public function test_make()
    {
        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/sample', $form);
        $this->assertStringContainsString('method="POST"', $form);

        $this->assertStringContainsString('<form method="POST" action="http://localhost/user/sample" accept-charset="UTF-8" class="batman" id="superman">', $form);
        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Name">Name</label>', $form);
        $this->assertStringContainsString('<input class="form-control" id="Name" name="name" type="text" value=""></div>', $form);
        $this->assertStringContainsString('<div class="col-md-12 d-flex justify-content-end"><button class="superman" type="submit">Save <span class="fas fa-save"></span></button></div></div></form>', $form);
    }

    public function test_make_with_extras()
    {
        $this->form->isCardForm = true;
        $this->form->disableOnSubmit = true;

        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/sample', $form);
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString('FormsJS_validate_submission', $form);

        $this->assertStringContainsString('<form method="POST" action="http://localhost/user/sample" accept-charset="UTF-8" class="batman" id="superman">', $form);
        $this->assertStringContainsString('<div class="card-body">', $form);
        $this->assertStringContainsString('<div class="card-footer">', $form);
        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Name">Name</label>', $form);
        $this->assertStringContainsString('<input class="form-control" id="Name" name="name" type="text" value=""></div>', $form);
        $this->assertStringContainsString('<div class="col-md-12 d-flex justify-content-end"><button class="superman" type="submit" data-formsjs-onclick="return window.FormsJS_validate_submission(this.form, &lt;i class=&quot;spinner-border spinner-border-sm&quot;&gt;&lt;/i&gt;);">Save <span class="fas fa-save"></span></button>', $form);
    }

    public function test_make_with_class_changes()
    {
        $form = app(UserSampleCreateForm::class)->make();

        $this->assertStringContainsString('http://localhost/user/sample', $form);
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString('DO NOT CLICK!', $form);

        $this->assertStringContainsString('<form method="POST" action="http://localhost/user/sample" accept-charset="UTF-8" class="batman-horizontal" id="batman">', $form);
        $this->assertStringContainsString('<div class="form-group row"><label class="col-md-2 col-form-label pt-0" for="Name">Name</label>', $form);
        $this->assertStringContainsString('<div class="col-md-10"><input class="form-control" id="Name" name="name" type="text" value="">', $form);
        $this->assertStringContainsString('<legend class="col-md-2 col-form-label pt-0"></legend><div class="col-md-10"><div class="form-check">', $form);
        $this->assertStringContainsString('<input class="form-check-input batman-style" id="Is_cool" type="checkbox" name="is_cool"><label class="form-check-label" for="Is_cool">Is Cool</label>', $form);

    }
}
