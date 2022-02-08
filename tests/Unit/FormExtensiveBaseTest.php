<?php

namespace Tests\Unit;

use Tests\TestCase;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Forms\BaseForm;
use Illuminate\Support\Facades\Route;
use Grafite\Forms\Fields\Checkbox;

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
        $this->assertStringContainsString('<input class="form-control" id="Name" name="name" type="text" value=""></div>', $form);
        $this->assertStringContainsString('<div class="col-md-12 d-flex justify-content-end"><button class="superman" type="submit">Save <span class="fas fa-save"></span></button></div></div></form>', $form);
    }

    public function testMakeWithExtras()
    {
        $this->form->isCardForm = true;
        $this->form->disableOnSubmit = true;

        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/sample', $form);
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString('Forms_validate_submission', $form);

        $this->assertStringContainsString('<form method="POST" action="http://localhost/user/sample" accept-charset="UTF-8" class="batman">', $form);
        $this->assertStringContainsString('<div class="card-body">', $form);
        $this->assertStringContainsString('<div class="card-footer">', $form);
        $this->assertStringContainsString('<div class="form-group"><label class="control-label" for="Name">Name</label>', $form);
        $this->assertStringContainsString('<input class="form-control" id="Name" name="name" type="text" value=""></div>', $form);
        $this->assertStringContainsString('<div class="col-md-12 d-flex justify-content-end"><button class="superman" type="submit" onclick="return window.Forms_validate_submission(this.form, &#039;&lt;i class=&quot;fas fa-circle-notch fa-spin mr-2&quot;&gt;&lt;/i&gt; Save &lt;span class=&quot;fas fa-save&quot;&gt;&lt;/span&gt;&#039;, this);">Save <span class="fas fa-save"></span></button>', $form);
    }

    public function testMakeWithClassChanges()
    {
        $form = app(UserSampleCreateForm::class)->make();

        $this->assertStringContainsString('http://localhost/user/sample', $form);
        $this->assertStringContainsString('method="POST"', $form);

        $this->assertStringContainsString('<form method="POST" action="http://localhost/user/sample" accept-charset="UTF-8" class="batman-horizontal">', $form);
        $this->assertStringContainsString('<div class="form-group row"><label class="col-md-2 col-form-label pt-0" for="Name">Name</label>', $form);
        $this->assertStringContainsString('<div class="col-md-10"><input class="form-control" id="Name" name="name" type="text" value="">', $form);
        $this->assertStringContainsString('<legend class="col-md-2 col-form-label pt-0"></legend><div class="col-md-10"><div class="form-check">', $form);
        $this->assertStringContainsString('<input class="form-check-input batman-style" id="Is_cool" type="checkbox" name="is_cool"><label class="form-check-label" for="Is_cool">Is Cool</label>', $form);
    }
}
