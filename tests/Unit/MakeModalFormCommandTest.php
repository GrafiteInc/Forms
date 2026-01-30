<?php

namespace Tests\Unit;

use Tests\TestCase;

class MakeModalFormCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->path = __DIR__.'/../../vendor/orchestra/testbench-core/laravel/app/View/Forms/UserCreateForm.php';

        @unlink($this->path);

        $this->artisan('make:modal-form', [
            'name' => 'UserCreate',
        ]);
    }

    public function test_file_has_been_generated()
    {
        $this->assertTrue(file_exists($this->path));

        $contents = file_get_contents($this->path);

        $this->assertStringContainsString('UserCreateForm', $contents);
    }
}
