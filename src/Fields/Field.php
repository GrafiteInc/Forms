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
        'null_value',
        'null_label',
        'model_options',
        'before',
        'after',
        'view',
        'attributes',
        'format',
    ];

    /**
     * Get type
     *
     * @return string
     */
    protected static function getType()
    {
        return 'text';
    }

    /**
     * Get factory
     *
     * @return string
     */
    protected static function getFactory()
    {
        return 'text(50)';
    }

    /**
     * Get options
     *
     * @return array
     */
    protected static function getOptions()
    {
        return [];
    }

    /**
     * Get select options for <select>
     *
     * @return array
     */
    protected static function getSelectOptions()
    {
        return [];
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected static function getAttributes()
    {
        return [];
    }

    /**
     * Make a field config for the FieldMaker
     *
     * @param string $name
     * @param array $options
     *
     * @return array
     */
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
                'value' => $options['value'] ?? null,
                'null_value' => $options['null_value'] ?? false,
                'null_label' => $options['null_label'] ?? 'None',
                'model_options' => [
                    'label' => $options['model_options']['label'] ?? 'name',
                    'value' => $options['model_options']['value'] ?? 'id',
                    'params' => $options['model_options']['params'] ?? null,
                    'method' => $options['model_options']['method'] ?? 'all',
                ],
                'before' => static::getWrappers($options, 'before'),
                'after' => static::getWrappers($options, 'after'),
                'view' => static::getView() ?? null,
                'attributes' => static::parseAttributes($options) ?? [],
                'factory' => static::getFactory(),
                'assets' => [
                    'js' => static::js(ucfirst($name), $options) ?? null,
                    'styles' => static::styles(ucfirst($name), $options) ?? null,
                    'scripts' => static::scripts($options) ?? null,
                    'stylesheets' => static::stylesheets($options) ?? null,
                ]
            ]
        ];
    }

    /**
     * Parse the options
     *
     * @param array $options
     *
     * @return array
     */
    protected static function parseOptions($options)
    {
        return array_merge(static::getOptions(), $options);
    }

    /**
     * Parse attributes for defaults
     *
     * @param array $options
     *
     * @return array
     */
    protected static function parseAttributes($options)
    {
        foreach (self::FIELD_OPTIONS as $option) {
            unset($options[$option]);
        }

        return array_merge(static::getAttributes(), $options);
    }

    /**
     * Get the wrappers for the input fields
     *
     * @param array $options
     * @param string $key
     *
     * @return mixed
     */
    protected static function getWrappers($options, $key)
    {
        $groupTextClass = config('formmaker.form.input-group-text', 'input-group-text');
        $class = config('formmaker.form.input-group-after', 'input-group-append');

        if ($key === 'before') {
            $class = config('formmaker.form.input-group-before', 'input-group-prepend');
        }

        if (isset($options[$key])) {
            return '<div class="' . $class . '">
                        <span class="' . $groupTextClass . '">' . $options[$key] . '</span>
                    </div>';
        }

        return null;
    }

    /**
     * View path for a custom template
     *
     * @return mixed
     */
    protected static function getView()
    {
        return null;
    }

    protected static function stylesheets($options)
    {
        return [];
    }

    protected static function styles($id, $options)
    {
        return null;
    }

    protected static function scripts($options)
    {
        return [];
    }

    protected static function js($id, $options)
    {
        return null;
    }
}
