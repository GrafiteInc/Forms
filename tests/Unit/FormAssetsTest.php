<?php

namespace Tests\Unit;

use Grafite\Forms\Fields\Quill;
use Grafite\Forms\Fields\Tags;
use Grafite\Forms\Forms\BaseForm;
use Grafite\Forms\Services\FormAssets;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserHistoryForm extends BaseForm
{
    public $withJsValidation = true;

    public $route = 'user.history';

    public $buttons = [
        'submit' => 'Save',
    ];

    public function fields()
    {
        return [
            Quill::make('history', [
                'toolbars' => [
                    'basic',
                ],
            ])
                ->option('theme', true)
                ->withoutLabel()
                ->option('upload_route', 'user.history'),
            Tags::make('qualities'),
        ];
    }

    public function scripts()
    {
        return "console.log('hello world')";
    }

    public function styles()
    {
        return '.hello { color: red; }';
    }
}

class FormAssetsTest extends TestCase
{
    protected $form;

    protected $formAssets;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formAssets = app(FormAssets::class);

        $this->session([
            'token' => 'tester',
        ]);

        Route::post('user/history')->name('user.history');

        $this->form = app(UserHistoryForm::class);
    }

    public function test_make()
    {
        $form = $this->form->make();

        $this->assertStringContainsString('http://localhost/user/history', $form);
        $this->assertStringContainsString('method="POST"', $form);

        $this->assertStringContainsString('History', $form);
    }

    public function test_make_form_scripts_and_styles()
    {
        $this->form->make();

        $assets = $this->formAssets->render();

        $this->assertStringContainsString("console.log('hello world')", $assets);
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $assets);
        $this->assertStringContainsString('.hello { color: red; }', $assets);
    }

    public function test_make_form_scripts()
    {
        $this->form->make();

        $assets = $this->formAssets->render('scripts');

        $this->assertStringContainsString("console.log('hello world')", $assets);
    }

    public function test_asset_counts()
    {
        $this->form->make();

        $this->assertEquals(4, count($this->formAssets->stylesheets));
        $this->assertEquals(7, count($this->formAssets->scripts));
        $this->assertEquals(3, count($this->formAssets->styles));
        $this->assertEquals(5, count($this->formAssets->js));
    }

    public function test_asset_contents()
    {
        $this->form->make();

        $assets = $this->formAssets->render();

        $this->assertStringContainsString('script', $assets);
        $this->assertStringContainsString('link', $assets);
        $this->assertStringContainsString('Quill', $assets);
        $this->assertStringContainsString('--tags-border-color', $assets);
        $this->assertStringContainsString('Tagify', $assets);
    }

    public function test_asset_default_contents()
    {
        $this->form->make();

        $assets = $this->formAssets->render();

        $this->assertStringContainsString('document.getElementsByClassName', $assets);
        $this->assertStringContainsString('.addEventListener', $assets);
        $this->assertStringContainsString('_fields', $assets);
    }
}
