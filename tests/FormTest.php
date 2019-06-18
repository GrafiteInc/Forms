<?php

use Grafite\FormMaker\Forms\Form;
use Grafite\FormMaker\Fields\Text;
use Grafite\FormMaker\Fields\Email;
use Illuminate\Support\Facades\Route;
use Grafite\FormMaker\Forms\ModelForm;

class FormTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('foo')->name('going.somewhere');

        $this->form = app(Form::class);
    }

    public function testOpen()
    {
        $form = $this->form->open([
            'url' => [
                'somewhere/special'
            ],
            'files' => true,
        ]);

        $this->assertStringContainsString('http://localhost/somewhere/special', $form);
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString('enctype="multipart/form-data"', $form);
    }

    public function testClose()
    {
        $form = $this->form->close();

        $this->assertStringContainsString('</form>', $form);
    }

    public function testToken()
    {
        $form = $this->form->token();

        $this->assertStringContainsString('name="_token"', $form);
    }

    public function testConfirm()
    {
        $form = $this->form->confirm('Are you sure?');

        $this->assertStringContainsString('Are you sure?', $form->confirmMessage);
    }

    public function testAction()
    {
        $form = $this->form->action('post', 'going.somewhere');

        $this->assertStringContainsString('_token', (string) $form);
        $this->assertStringContainsString('localhost/foo', (string) $form);
        $this->assertStringContainsString('POST', (string) $form);
        $this->assertStringContainsString('btn btn-primary', (string) $form);
    }

    public function testActionWithConfirm()
    {
        $form = $this->form->confirm('Are you sure?')->action('post', 'going.somewhere');

        $this->assertStringContainsString('_token', (string) $form);
        $this->assertStringContainsString('localhost/foo', (string) $form);
        $this->assertStringContainsString('POST', (string) $form);
        $this->assertStringContainsString('btn btn-primary', (string) $form);
        $this->assertStringContainsString('Are you sure?', (string) $form);
    }
}