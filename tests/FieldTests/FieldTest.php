<?php

use Grafite\FormMaker\Fields\Text;

class FieldTest extends TestCase
{
    public function testOpen()
    {
        $field = Text::make('address', [
            'placeholder' => 'address'
        ]);

        $this->assertEquals('address', array_key_first($field));
        $this->assertEquals('address', $field['address']['placeholder']);
    }
}