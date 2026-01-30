<?php

namespace Tests\Unit;

use Grafite\Forms\Forms\Form;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('foo')->name('going.somewhere');

        $this->form = app(Form::class);
    }

    public function test_open()
    {
        $form = $this->form->open([
            'url' => [
                'somewhere/special',
            ],
            'files' => true,
        ]);

        $this->assertStringContainsString('http://localhost/somewhere/special', $form);
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString('enctype="multipart/form-data"', $form);
    }

    public function test_close()
    {
        $form = $this->form->close();

        $this->assertStringContainsString('</form>', $form);
    }

    public function test_token()
    {
        $form = $this->form->token();

        $this->assertStringContainsString('name="_token"', $form);
    }

    public function test_confirm()
    {
        $form = $this->form->confirm('Are you sure?');

        $this->assertStringContainsString('Are you sure?', $form->confirmMessage);
    }

    public function test_action()
    {
        $form = $this->form->action('post', 'going.somewhere');

        $this->assertStringContainsString('_token', (string) $form);
        $this->assertStringContainsString('localhost/foo', (string) $form);
        $this->assertStringContainsString('POST', (string) $form);
        $this->assertStringContainsString('btn btn-primary', (string) $form);
    }

    public function test_action_with_confirm()
    {
        $form = $this->form->confirm('Are you sure?')->action('post', 'going.somewhere');

        $this->assertStringContainsString('_token', (string) $form);
        $this->assertStringContainsString('localhost/foo', (string) $form);
        $this->assertStringContainsString('POST', (string) $form);
        $this->assertStringContainsString('btn btn-primary', (string) $form);
        $this->assertStringContainsString('Are you sure?', (string) $form);
    }
}
