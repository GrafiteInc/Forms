<?php

use Grafite\FormMaker\Forms\Form;
use Grafite\FormMaker\Fields\Text;
use Illuminate\Support\Facades\Route;

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

        // dd($this->form->action('post', 'going.somewhere'));
        // dd(Text::make('address', null, [
        //     'placeholder' => 'address'
        // ]));

        // dd($this->form->submit('Save'));

        // dd($this->form->open([
        //     'url' => [
        //         'somewhere/special'
        //     ],
        //     'files' => true,
        // ]));
    }

    public function testModel()
    {
        # code...
    }

    public function testClose()
    {
        # code...
    }

    public function testToken()
    {
        # code...
    }

    public function testConfirm()
    {
        # code...
    }

    public function testAction()
    {
        # code...
    }
}