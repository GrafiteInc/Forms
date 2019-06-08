<?php

namespace Grafite\FormMaker\Builders;

use Grafite\FormMaker\Services\InputCalibrator;

class FieldBuilder
{
    /**
     * Create a submit button element.
     *
     * @param  string $value
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function submit($value = null, $options = [])
    {
        return $this->makeInput('submit', null, $value, $options);
    }

    /**
     * Make an html button
     *
     * @param string $value
     * @param array $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function button($value = null, $options = [])
    {
        if (! array_key_exists('type', $options)) {
            $options['type'] = 'button';
        }

        return $this->toHtmlString('<button' . $this->attributes($options) . '>' . $value . '</button>');
    }

    /**
     * Make an input string
     *
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @param array $options
     *
     * @return string
     */
    public function makeInput($type, $name, $value, $options)
    {
        return '<input '.$this->attributes($options).' name="'.$name.'" type="'.$type.'" value="'.$value.'">';
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function attributes($attributes)
    {
        $html = [];

        foreach ((array) $attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if (! is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    public function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            return $value;
        }

        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="' . implode(' ', $value) . '"';
        }

        if (! is_null($value)) {
            return $key . '="' . e($value, false) . '"';
        }
    }

    /**
     * Make text input.
     *
     * @param array  $config
     * @param string $population
     * @param mixed $custom
     *
     * @return string
     */
    public function makeFile($config, $population, $custom)
    {
        $multiple = $this->isMultiple($config, 'multiple');
        $multipleArray = $this->isMultiple($config, '[]');

        $label = '<label class="custom-file-label" for="'.$this->getId($config).'">Choose file</label>';

        return '<div class="custom-file"><input '.$this->processCustom($custom).' id="'.$this->getId($config).'" class="custom-file-input" type="file" name="'.$config['name'].$multipleArray.'" '.$multiple.'>'.$label.'</div>';
    }

    /**
     * Make a hidden input.
     *
     * @param array  $config
     * @param string $population
     * @param mixed $custom
     *
     * @return string
     */
    public function makeHidden($config, $population, $custom = [])
    {
        return '<input '.$this->processCustom($custom).' id="'.$this->getId($config).'" name="'.$config['name'].'" type="hidden" value="'.$population.'">';
    }

    /**
     * Make text input.
     *
     * @param array  $config
     * @param string $population
     * @param mixed $custom
     *
     * @return string
     */
    public function makeText($config, $population, $custom)
    {
        return '<textarea '.$this->processCustom($custom).' id="'.$this->getId($config).'" class="'.$config['class'].'" name="'.$config['name'].'" placeholder="'.$config['placeholder'].'">'.$population.'</textarea>';
    }

    /**
     * Make a select input.
     *
     * @param array  $config
     * @param string $selected
     * @param mixed $custom
     *
     * @return string
     */
    public function makeSelected($config, $selected, $custom)
    {
        $options = $prefix = $suffix = '';

        if (isset($config['config']['multiple'])) {
            $custom = 'multiple';
            $config['name'] = $config['name'].'[]';
        }

        // if (config('form-maker.form.orientation') === 'horizontal') {
        //     $prefix = '<div class="'.config('form-maker.form.input-column').'">';
        //     $suffix = '</div>';
        // }

        foreach ($config['config']['options'] as $key => $value) {
            $selectedValue = '';

            if (isset($config['config']['multiple']) && is_object($selected)) {
                if (in_array($value, $selected->toArray())) {
                    $selectedValue = 'selected';
                }
            } else {
                if ($selected == '') {
                    $selectedValue = ((string) $config['objectValue'] === (string) $value) ? 'selected' : '';
                } else {
                    if (isset($config['objectValue']) && is_array(json_decode($config['objectValue']))) {
                        $selectedValue = (in_array($value, json_decode($config['objectValue']))) ? 'selected' : '';
                    } else {
                        $selectedValue = ((string) $selected === (string) $value) ? 'selected' : '';
                    }
                }
            }

            $options .= '<option value="'.$value.'" '.$selectedValue.'>'.$key.'</option>';
        }

        return $prefix.'<select '.$this->processCustom($custom).' id="'.$this->getId($config).'" class="'.$config['class'].'" name="'.$config['name'].'">'.$options.'</select>'.$suffix;
    }

    /**
     * Make a checkbox.
     *
     * @param array  $config
     * @param string $selected
     * @param mixed $custom
     *
     * @return string
     */
    public function makeCheckbox($config, $selected, $custom)
    {
        if (str_contains($config['class'], 'form-control')) {
            $config['class'] = str_replace('form-control', '', $config['class']);
        }

        return '<input '.$this->processCustom($custom).' id="'.$this->getId($config).'" '.$selected.' type="checkbox" name="'.$config['name'].'" class="'. $config['class'] .'">';
    }

    /**
     * Make a radio input.
     *
     * @param array  $config
     * @param string $selected
     * @param mixed $custom
     *
     * @return string
     */
    public function makeRadio($config, $selected, $custom)
    {
        if (str_contains($config['class'], 'form-control')) {
            $config['class'] = str_replace('form-control', '', $config['class']);
        }

        return '<input '.$this->processCustom($custom).' id="'.$this->getId($config).'" '.$selected.' type="radio" name="'.$config['name'].'" class="'. $config['class'] .'">';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship based
    |--------------------------------------------------------------------------
    */

    /**
     * Make a relationship input.
     *
     * @param array  $config
     * @param string $label
     * @param string $value
     * @param mixed $custom
     *
     * @return string
     */
    public function makeRelationship($config, $label = 'name', $value = 'id', $custom = '')
    {
        $object = $config['object'];

        if (isset($config['config']['relationship'])) {
            $relationship = $config['config']['relationship'];
        } else {
            $relationship = $config['name'];
        }

        // Removes the array indication for select multiple
        $relationship = str_replace('[]', '', $relationship);

        $method = 'all';

        if (!is_object($config['config']['model'])) {
            $class = app()->make($config['config']['model']);
        } else {
            $class = $config['config']['model'];
        }

        if (isset($config['config']['method'])) {
            $method = $config['config']['method'];
        }

        if (isset($config['config']['params'])) {
            $items = $class->$method($config['config']['params']);
        } else {
            $items = $class->$method();
        }

        if (isset($config['config']['nullable']) && $config['config']['nullable'] === true) {
            $config['config']['options']['- Select -'] = null;
        }
        foreach ($items as $item) {
            $config['config']['options'][$item->$label] = $item->$value;
        }

        if (!isset($config['config']['selected'])) {
            if (!isset($config['config']['multiple'])) {
                $selected = '';

                if (is_object($object) && method_exists($object, $relationship)) {
                    if ($object->$relationship()->first()) {
                        $selected = $object->$relationship()->first()->$value;
                    }
                }

                $relationship = str_replace('_id', '', $relationship);

                if (method_exists($object, $relationship)) {
                    if ($object->$relationship()->first()) {
                        $selected = $object->$relationship()->first()->$value;
                    }
                }
            } else {
                $selected = $class->$method()->pluck($value, $label);
            }
        } else {
            $selected = $config['config']['selected'];
        }

        return $this->makeSelected($config, $selected, $custom);
    }

    /**
     * Generate a standard HTML input string.
     *
     * @param array $config Config array
     *
     * @return string
     */
    public function makeHTMLInputString($config)
    {
        $custom = $this->getCustom($config);
        $multiple = $this->isMultiple($config, 'multiple');
        $multipleArray = $this->isMultiple($config, '[]');
        $floatingNumber = $this->getFloatingNumber($config);
        $population = $this->getPopulation($config);

        if (is_array($config['objectValue']) && $config['type'] === 'file') {
            $population = '';
        }

        $inputString = '<input '.$this->processCustom($custom).' id="'.$this->getId($config).'" class="'.$config['class'].'" type="'.$config['type'].'" name="'.$config['name'].$multipleArray.'" '.$floatingNumber.' '.$multiple.' '.$population.' placeholder="'.$config['placeholder'].'">';

        return $inputString;
    }

    /**
     * Is the config a multiple?
     *
     * @param array  $config
     * @param string $response
     *
     * @return bool
     */
    public function isMultiple($config, $response)
    {
        if (isset($config['config']['multiple'])) {
            return $response;
        }

        return '';
    }

    /**
     * Get the population.
     *
     * @param array $config
     *
     * @return string
     */
    public function getPopulation($config)
    {
        if ($config['populated'] && ($config['name'] !== $config['objectValue'])) {

            if ($config['type'] == 'date' && method_exists($config['objectValue'], 'format')) {
                $format = (isset($config['format'])) ? $config['format'] : 'Y-m-d';
                $config['objectValue'] = $config['objectValue']->format($format);
            }

            return 'value="'.html_entity_decode($config['objectValue']).'"';
        }

        return '';
    }

    /**
     * Get an items ID
     *
     * @param  array $config
     *
     * @return string
     */
    public function getId($config)
    {
        return app(InputCalibrator::class)->getId($config['name'], $config);
    }

    /**
     * Get the custom details.
     *
     * @param array $config
     *
     * @return string
     */
    public function getCustom($config)
    {
        if (isset($config['config']['custom'])) {
            return $config['config']['custom'];
        }

        return '';
    }

    /**
     * Get the floating number.
     *
     * @param array $config
     *
     * @return string
     */
    public function getFloatingNumber($config)
    {
        if ($config['inputType'] === 'float' || $config['inputType'] === 'decimal') {
            return 'step="any"';
        }

        return '';
    }

    /**
     * Process custom attributes since there can be many
     *
     * @param  mixed $custom
     *
     * @return string
     */
    public function processCustom($custom)
    {
        if (is_array($custom)) {
            return $this->attributes($custom);
        }

        return $custom;
    }

    /**
     * Transform the string to an Html serializable object
     *
     * @param $html
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function toHtmlString($html)
    {
        return new HtmlString($html);
    }
}
