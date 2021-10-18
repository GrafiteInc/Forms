<?php


namespace Tests\Unit;

use Tests\TestCase;

class FormHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);
    }

    public function testSingleField()
    {
        $field = form()->makeField(\Grafite\Forms\Fields\Text::class, 'name');

        $this->assertStringContainsString('type="text"', $field);
        $this->assertStringContainsString('name="name"', $field);
    }
}
