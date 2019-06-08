<?php

namespace Grafite\FormMaker\Fields;

class Field
{
    protected static function getType()
    {
        return 'string';
    }

    protected static function getOptions()
    {
        return [];
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getIgnoredOptions()
    {
        return [
            'type',
            'label',
            'custom',
            'placeholder',
            'options',
            'multiple',
            'model',
            'label',
            'value',
            'class',
            'before',
            'after',
        ];
    }

    public static function make($name, $options = [])
    {
        $options = static::parseOptions($options);

        return [
            $name => [
                'type' => static::getType(),
                'custom' => static::parseAttributes($options),
                'placeholder' => $options['placeholder'] ?? null,
                'alt_name' => $options['label'] ?? null,
                'options' => $options['options'] ?? null,
                'class' => $options['class'] ?? null,
                'model' => $options['model'] ?? null,
                'multiple' => $options['multiple'] ?? null,
                'multiple' => $options['multiple'] ?? null,
                'label' => $options['label'] ?? null,
                'value' => $options['value'] ?? null,
                'before' => static::getWrappers($options, 'before'),
                'after' => static::getWrappers($options, 'after'),
            ]
        ];
    }

    protected static function parseOptions($options)
    {
        $staticOptions = static::getOptions();

        return array_merge($options, $staticOptions);
    }

    protected static function parseAttributes($options)
    {
        $ignoredOptions = static::getIgnoredOptions();
        $staticOptions = static::getAttributes();

        $uniqueOptions = [];

        foreach ($options as $key => $option) {
            if (!in_array($key, $ignoredOptions)) {
                $uniqueOptions[$key] = $option;
            }
        }

        return array_merge($uniqueOptions, $staticOptions);
    }

    protected static function getWrappers($options, $key)
    {
        $class = 'append';

        if ($key === 'before') {
            $class = 'prepend';
        }

        if (isset($options[$key])) {
            return '<div class="input-group-'.$class.'">
                        <span class="input-group-text">'.$options[$key].'</span>
                    </div>';
        }

        return null;
    }
}



