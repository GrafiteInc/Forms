<?php

namespace Grafite\FormMaker\Fields;

class Field
{
    const FIELD_OPTIONS = [
        'type',
        'options',
        'legend',
        'label',
        'model',
        'model_options',
        'before',
        'after',
        'view',
        'attributes',
        'format',
    ];

    protected static function getType()
    {
        return 'text';
    }

    protected static function getOptions()
    {
        return [];
    }

    protected static function getSelectOptions()
    {
        return [];
    }

    protected static function getAttributes()
    {
        return [];
    }

    public static function make($name, $options = [])
    {
        $options = static::parseOptions($options);

        return [
            $name => [
                'type' => static::getType(),
                'options' => array_merge(static::getSelectOptions(), $options['options'] ?? []),
                'format' => $options['format'] ?? null,
                'legend' => $options['legend'] ?? null,
                'label' => $options['label'] ?? null,
                'model' => $options['model'] ?? null,
                'model_options' => [
                    'label' => $options['label'] ?? 'name',
                    'value' => $options['value'] ?? 'id',
                    'params' => $options['params'] ?? null,
                    'method' => $options['method'] ?? 'all',
                ],
                'before' => static::getWrappers($options, 'before'),
                'after' => static::getWrappers($options, 'after'),
                'view' => static::getView() ?? null,
                'attributes' => static::parseAttributes($options) ?? [],
            ]
        ];
    }

    protected static function parseOptions($options)
    {
        return array_merge(static::getOptions(), $options);
    }

    protected static function parseAttributes($options)
    {
        foreach (self::FIELD_OPTIONS as $option) {
            unset($options[$option]);
        }

        return array_merge(static::getAttributes(), $options);
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

    protected static function getView()
    {
        return null;
    }
}
