<?php

class MakeFieldCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->path = __DIR__.'/../vendor/orchestra/testbench-core/laravel/app/Http/Forms/Fields/SpecialField.php';

        @unlink($this->path);

        $this->artisan('make:field', [
            'name' => 'Special'
        ]);
    }

    public function testFileHasBeenGenerated()
    {
        $this->assertTrue(file_exists($this->path));

        $contents = file_get_contents($this->path);

        $this->assertStringContainsString('SpecialField', $contents);
    }
}
