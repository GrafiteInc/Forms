<?php

namespace Grafite\Forms\Fields;

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
        'visible',
        'sortable',
        'wrapper',
        'table-class',
        'label-class',
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
                'visible'  => $options['visible'] ?? true,
                'wrapper'  => $options['wrapper'] ?? true,
                'sortable'  => $options['sortable'] ?? false,
                'format' => $options['format'] ?? null,
                'legend' => $options['legend'] ?? null,
                'label' => $options['label'] ?? null,
                'model' => $options['model'] ?? null,
                'value' => $options['value'] ?? null,
                'null_value' => $options['null_value'] ?? false,
                'null_label' => $options['null_label'] ?? 'None',
                'table-class' => $options['table-class'] ?? null,
                'model_options' => [
                    'label' => $options['model_options']['label'] ?? 'name',
                    'value' => $options['model_options']['value'] ?? 'id',
                    'params' => $options['model_options']['params'] ?? null,
                    'method' => $options['model_options']['method'] ?? 'all',
                ],
                'before' => static::getWrappers($options, 'before'),
                'after' => static::getWrappers($options, 'after'),
                'view' => static::getView() ?? null,
                'template' => static::getTemplate($options) ?? null,
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
     * Add the field's custom options to the default list
     *
     * @return array
     */
    protected static function getFieldOptions()
    {
        return array_merge(self::FIELD_OPTIONS, static::fieldOptions());
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
        foreach (self::getFieldOptions() as $option) {
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
     * Extra options for a field we don't need as attributes
     *
     * @return array
     */
    protected static function fieldOptions()
    {
        return [];
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

    /**
     * Field template string, performs a basic string swap
     * of name, id, field, label, errors etc
     *
     * @return string
     */
    protected static function getTemplate($options)
    {
        return null;
    }

    /**
     * Field related stylesheets
     *
     * @param array $options
     * @return array
     */
    protected static function stylesheets($options)
    {
        return [];
    }

    /**
     * Field related styles
     *
     * @param string $id
     * @param array $options
     * @return string|null
     */
    protected static function styles($id, $options)
    {
        return null;
    }

    /**
     * Field related scripts
     *
     * @param array $options
     * @return array
     */
    protected static function scripts($options)
    {
        return [];
    }

    /**
     * Field related JavaScript
     *
     * @param string $id
     * @param array $options
     * @return string|null
     */
    protected static function js($id, $options)
    {
        return null;
    }
}
