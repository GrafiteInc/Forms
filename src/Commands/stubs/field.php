<?php

namespace DummyNamespace;

use Grafite\FormMaker\Fields\Field;

class DummyClass extends Field
{
    /**
     * Input type
     *
     * @return string
     */
    protected static function getType()
    {
        return 'text';
    }

    /**
     * Input attributes
     *
     * @return array
     */
    protected static function getAttributes()
    {
        return [];
    }

    /**
     * Field maker options
     *
     * @return array
     */
    protected static function getOptions()
    {
        return [];
    }

    /**
     * View path for custom templates
     *
     * @return string
     */
    protected static function getView()
    {
        return null;
    }
}
