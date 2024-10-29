<?php

namespace Tests\Unit;

use Tests\TestCase;
use Grafite\Forms\Builders\AttributeBuilder;

class AttributeBuilderTest extends TestCase
{
    protected $app;
    protected $builder;

    public function setUp(): void
    {
        parent::setUp();

        $this->builder = app(AttributeBuilder::class);
    }

    public function testAttributes()
    {
        $test = $this->builder->render([
            'placeholder' => 'thing'
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('placeholder="thing"', $test);
    }

    public function testAttributeElement()
    {
        $test = $this->builder->attributeElement('placeholder', 'thing');

        $this->assertTrue(is_string($test));
        $this->assertEquals('placeholder="thing"', $test);
    }
}
