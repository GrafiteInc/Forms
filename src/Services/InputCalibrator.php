<?php

namespace Grafite\FormMaker\Services;

/**
 * Input Calibration.
 */
class InputCalibrator
{
    protected $columnTypes = [
        'integer',
        'string',
        'datetime',
        'date',
        'float',
        'binary',
        'blob',
        'boolean',
        'datetimetz',
        'time',
        'array',
        'json_array',
        'object',
        'decimal',
        'bigint',
        'smallint',
        'one-one',
        'one-many',
    ];

    /**
     * Get the content of the input.
     *
     * @param array $config
     *
     * @return string
     */
    public function getPopulation($config)
    {
        return ($config['populated']) ? $config['objectValue'] : '';
    }

    /**
     * Customizable input name
     *
     * @param  string $name
     * @param  array $config
     *
     * @return string
     */
    public function getName($name, $config)
    {
        if (isset($config['name'])) {
            $name = $config['name'];
        }

        return $name;
    }

    /**
     * Customizable input name
     *
     * @param  string $name
     * @param  array $config
     *
     * @return string
     */
    public function getId($name, $config)
    {
        $inputId = str_replace('[]', '', ucfirst($name));

        if (isset($config['id'])) {
            $inputId = $config['id'];
        }

        return $inputId;
    }

    /**
     * Has been selected.
     *
     * @param array  $config
     * @param string $checkType Type of checkmark
     *
     * @return bool
     */
    public function isSelected($config, $checkType)
    {
        $selected = false;

        if (!is_object($config['objectValue'])) {
            $selected = (isset($config['inputs'][$config['name']])
                || isset($config['config']['selected'])
                || $config['objectValue'] === 'on'
                || ($config['objectValue'] == 1 && $checkType == 'checked')
                || is_array(json_decode($config['objectValue']))) ? $checkType : '';
        }

        return $selected;
    }

    /**
     * Check type of checkbox/ radio.
     *
     * @param array $config
     * @param array $checkboxInputs
     *
     * @return string
     */
    public function checkType($config, $checkboxInputs)
    {
        $checkType = (in_array($config['inputType'], $checkboxInputs)) ? 'checked' : 'selected';

        return $checkType;
    }

    /**
     * Get attributes.
     *
     * @param array $config
     *
     * @return string
     */
    public function getField($config, $field, $default = '')
    {
        $data = (isset($config['config'][$field])) ? $config['config'][$field] : $default;

        return $data;
    }

    /**
     * Create the placeholder.
     *
     * @param array  $field  Field from Column Array
     * @param string $column Column name
     *
     * @return string
     */
    public function placeholder($field, $column)
    {
        if (!is_array($field) && !in_array($field, $this->columnTypes)) {
            return ucfirst($field);
        }

        if (strpos($column, '[') > 0) {
            $nested = explode('[', str_replace(']', '', $column));
            $column = implode(' ', $nested);
        }

        $alt_name = (isset($field['alt_name'])) ? $field['alt_name'] : ucfirst($column);
        $placeholder = (isset($field['placeholder'])) ? $field['placeholder'] : $this->cleanString($alt_name);

        return $placeholder;
    }

    /**
     * Clean the string for the column name swap.
     *
     * @param string $string Original column name
     *
     * @return string
     */
    public function cleanString($string)
    {
        return str_replace('_', ' ', $string);
    }
}
