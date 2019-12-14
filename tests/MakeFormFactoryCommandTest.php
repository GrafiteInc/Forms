<?php

class MakeFormFactoryCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->path = __DIR__.'/../vendor/orchestra/testbench-core/laravel/database/factories/UserFactory.php';

        @unlink($this->path);

        $this->artisan('make:form-factory', [
            'form' => UserForm::class
        ]);
    }

    public function testFileHasBeenGenerated()
    {
        $this->assertTrue(file_exists($this->path));

        $contents = file_get_contents($this->path);

        $this->assertStringContainsString('$faker->name', $contents);
        $this->assertStringContainsString('$faker->email', $contents);
        $this->assertStringContainsString('define(User::class', $contents);
    }
}
