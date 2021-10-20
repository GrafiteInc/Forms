<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Services\FieldConfigProcessor;

class Field
{
    public const FIELD_OPTIONS = [
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
        'table_class',
        'label_class',
        'template',
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
        $field = new static();
        $options = static::parseOptions($options);
        $options['type'] = static::getType() ?? 'text';

        if (! in_array($options['type'], ['relationship', 'select'])) {
            foreach ($options as $key => $option) {
                if (! in_array($key, static::getFieldOptions())) {
                    $options['options'][$key] = $option;
                }
            }
        }

        $options['options'] = array_merge(static::getSelectOptions(), $options['options'] ?? []);
        $options['before'] = static::getWrappers($options, 'before');
        $options['after'] = static::getWrappers($options, 'after');
        $options['view'] = static::getView() ?? null;
        $options['template'] = static::getTemplate($options) ?? null;
        $options['attributes'] = static::parseAttributes($options) ?? [];
        $options['factory'] = static::getFactory();

        $config = (new FieldConfigProcessor($name, $options, $field));

        return $config;
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
        foreach (static::getFieldOptions() as $option) {
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
        $groupTextClass = config('forms.form.input-group-text', 'input-group-text');
        $class = config('forms.form.input-group-after', 'input-group-append');

        if ($key === 'before') {
            $class = config('forms.form.input-group-before', 'input-group-prepend');
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
    public static function getTemplate($options)
    {
        return null;
    }

    /**
     * Field related stylesheets
     *
     * @param array $options
     * @return array
     */
    public static function stylesheets($options)
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
    public static function styles($id, $options)
    {
        return null;
    }

    /**
     * Field related scripts
     *
     * @param array $options
     * @return array
     */
    public static function scripts($options)
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
    public static function js($id, $options)
    {
        return null;
    }
}
