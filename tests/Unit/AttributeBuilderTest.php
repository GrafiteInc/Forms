<?php

namespace Tests\Unit;

use Grafite\Forms\Builders\AttributeBuilder;
use Tests\TestCase;

class AttributeBuilderTest extends TestCase
{
    protected $app;

    protected $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = app(AttributeBuilder::class);
    }

    public function test_attributes()
    {
        $test = $this->builder->render([
            'placeholder' => 'thing',
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('placeholder="thing"', $test);
    }

    public function test_attribute_element()
    {
        $test = $this->builder->attributeElement('placeholder', 'thing');

        $this->assertTrue(is_string($test));
        $this->assertEquals('placeholder="thing"', $test);
    }
}
