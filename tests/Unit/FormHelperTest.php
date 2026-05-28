<?php

namespace Tests\Unit;

use Grafite\Forms\Fields\Text;
use Tests\TestCase;

class FormHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->session([
            'token' => 'tester',
        ]);
    }

    public function test_single_field()
    {
        $field = form()->makeField(Text::class, 'name');

        $this->assertStringContainsString('type="text"', $field);
        $this->assertStringContainsString('name="name"', $field);
    }
}
