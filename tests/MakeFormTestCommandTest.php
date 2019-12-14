<?php

class MakeFormTestCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->path = __DIR__.'/../vendor/orchestra/testbench-core/laravel/tests/Feature/UserTest.php';

        @unlink($this->path);

        $this->artisan('make:form-test', [
            'form' => UserForm::class
        ]);
    }

    public function testFileHasBeenGenerated()
    {
        $this->assertTrue(file_exists($this->path));

        $contents = file_get_contents($this->path);

        $this->assertStringContainsString('UserTest', $contents);
        $this->assertStringContainsString('testIndex()', $contents);
        $this->assertStringContainsString('assertOk()', $contents);
    }
}
