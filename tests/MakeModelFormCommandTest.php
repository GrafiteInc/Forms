<?php

class MakeModelFormCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->path = __DIR__.'/../vendor/orchestra/testbench-core/laravel/app/Http/Forms/UserForm.php';

        @unlink($this->path);

        $this->artisan('make:model-form', [
            'name' => 'User'
        ]);
    }

    public function testFileHasBeenGenerated()
    {
        $this->assertTrue(file_exists($this->path));

        $contents = file_get_contents($this->path);

        $this->assertStringContainsString('UserForm', $contents);
    }
}