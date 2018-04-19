<?php

namespace Grafite\FormMaker\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Grafite\FormMaker\Generators\HtmlGenerator;

/**
 * $this->elper to make an HTML input.
 */
class InputMaker
{
    protected $htmlGenerator;

    protected $inputCalibrator;

    protected $inputGroups = [
        'text' => [
            'text',
            'textarea',
        ],
        'select' => [
            'select',
        ],
        'hidden' => [
            'hidden',
        ],
        'checkbox' => [
            'checkbox',
            'checkbox-inline',
        ],
        'radio' => [
            'radio',
            'radio-inline',
        ],
        'relationship' => [
            'relationship',
        ],
    ];

    protected $standardMethods = [
        'makeHidden',
        'makeText',
    ];

    protected $selectedMethods = [
        'makeSelected',
        'makeCheckbox',
        'makeRadio',
    ];

    public function __construct()
    {
        $this->htmlGenerator = new HtmlGenerator();
        $this->inputCalibrator = new InputCalibrator();
    }

    /**
     * Create the input HTML.
     *
     * @param string       $name        Column/ Input name
     * @param array        $config      Array of config info for the input
     * @param object|array $object      Object or Table Object
     * @param string       $class       CSS class
     * @param bool         $reformatted Clean the labels and placeholder values
     * @param bool         $populated   Set the value of the input to the object's value
     *
     * @return string
     */
    public function create($name, $config, $object = null, $class = null, $reformatted = false, $populated = true)
    {
        $defaultConfig = include __DIR__.'/../../config/form-maker.php';

        if (is_null($class)) {
            $class = config('form-maker.forms.form-class', 'form-control');
        }

        $inputConfig = [
            'populated' => $populated,
            'name' => $this->inputCalibrator->getName($name, $config),
            'id' => $this->inputCalibrator->getId($name, $config),
            'class' => $this->prepareTheClass($class, $config),
            'config' => $config,
            'inputTypes' => Config::get('form-maker.inputTypes', $defaultConfig['inputTypes']),
            'inputs' => $this->getInput(),
            'object' => $object,
            'objectValue' => $this->getObjectValue($object, $name),
            'placeholder' => $this->inputCalibrator->placeholder($config, $name),
        ];

        $inputConfig = $this->refineConfigs($inputConfig, $reformatted, $name, $config);

        return $this->inputStringPreparer($inputConfig);
    }

    /**
     * Get the object value from the object with the name.
     *
     * @param mixed  $object
     * @param string $name
     *
     * @return mixed
     */
    public function getObjectValue($object, $name)
    {
        if (is_object($object) && isset($object->$name) && !method_exists($object, $name)) {
            return $object->$name;
        }

        // If its a nested value like meta[user[phone]]
        if (strpos($name, '[') > 0) {
            $nested = explode('[', str_replace(']', '', $name));
            $final = $object;
            foreach ($nested as $property) {
                if (!empty($property) && isset($final->{$property})) {
                    $final = $final->{$property};
                } elseif (is_object($final) && is_null($final->{$property})) {
                    $final = '';
                }
            }

            return $final;
        }

        return '';
    }

    /**
     * Input string preparer.
     *
     * @param array $config
     *
     * @return string
     */
    public function inputStringPreparer($config)
    {
        $inputString = '';
        $beforeAfterCondition = ($this->before($config) > '' || $this->after($config) > '');
        $method = $this->getGeneratorMethod($config['inputType']);

        if ($beforeAfterCondition) {
            $inputString .= '<div class="'.config('form-maker.form.before_after_input_wrapper', 'input-group').'">';
        }

        $inputString .= $this->before($config);
        $inputString .= $this->inputStringGenerator($config);
        $inputString .= $this->after($config);

        if ($beforeAfterCondition) {
            $inputString .= '</div>';
        }

        if (config('form-maker.form.orientation') == 'horizontal' && !in_array($method, $this->selectedMethods)) {
            return '<div class="'.config('form-maker.form.input-column', '').'">'.$inputString.'</div>';
        }

        return $inputString;
    }

    /**
     * Create a label for an input.
     *
     * @param string $name
     * @param array  $attributes
     *
     * @return string
     */
    public function label($name, $attributes = [])
    {
        $attributeString = '';

        if (!isset($attributes['for'])) {
            $attributeString = 'for="'.$name.'"';
        }

        foreach ($attributes as $key => $value) {
            $attributeString .= $key.'="'.$value.'" ';
        }

        return '<label '.$attributeString.'>'.$name.'</label>';
    }

    /**
     * Before input.
     *
     * @param array $config
     *
     * @return string
     */
    private function before($config)
    {
        $before = (isset($config['config']['before'])) ? $config['config']['before'] : '';

        return $before;
    }

    /**
     * After input.
     *
     * @param array $config
     *
     * @return string
     */
    private function after($config)
    {
        $after = (isset($config['config']['after'])) ? $config['config']['after'] : '';

        return $after;
    }

    /**
     * Prepare the input class.
     *
     * @param string $class
     *
     * @return string
     */
    public function prepareTheClass($class, $config)
    {
        $finalizedClass = $class;

        if (isset($config['class'])) {
            $finalizedClass .= ' '.$config['class'];
        }

        return $finalizedClass;
    }

    /**
     * Get inputs.
     *
     * @return array
     */
    public function getInput()
    {
        $input = [];

        if (Session::isStarted()) {
            $input = Request::old();
        }

        return $input;
    }

    /**
     * Set the configs.
     *
     * @param array  $config
     * @param bool   $reformatted
     * @param string $name
     * @param array  $config
     *
     * @return array
     */
    private function refineConfigs($inputConfig, $reformatted, $name, $config)
    {
        // If validation inputs are available lets prepopulate the fields!
        if (!empty($inputConfig['inputs']) && isset($inputConfig['inputs'][$name])) {
            $inputConfig['populated'] = true;
            $inputConfig['objectValue'] = $inputConfig['inputs'][$name];
        } elseif (isset($inputConfig['config']['default_value'])) {
            $inputConfig['objectValue'] = $inputConfig['config']['default_value'];
        }

        if ($reformatted) {
            $inputConfig['placeholder'] = $this->inputCalibrator->cleanString($this->inputCalibrator->placeholder($config, $name));
        }

        if (!isset($config['type'])) {
            if (is_array($config)) {
                $inputConfig['inputType'] = 'string';
            } else {
                $inputConfig['inputType'] = $config;
            }
        } else {
            $inputConfig['inputType'] = $config['type'];
        }

        return $inputConfig;
    }

    /**
     * The input string generator.
     *
     * @param array $config Config
     *
     * @return string
     */
    private function inputStringGenerator($config)
    {
        $config = $this->prepareObjectValue($config);
        $population = $this->inputCalibrator->getPopulation($config);
        $checkType = $this->inputCalibrator->checkType($config, $this->inputGroups['checkbox']);
        $selected = $this->inputCalibrator->isSelected($config, $checkType);
        $custom = $this->inputCalibrator->getField($config, 'custom');
        $method = $this->getGeneratorMethod($config['inputType']);

        if (in_array($method, $this->standardMethods)) {
            $inputString = $this->htmlGenerator->$method($config, $population, $custom);
        } elseif (in_array($method, $this->selectedMethods)) {
            // add extra class

            $inputString = $this->htmlGenerator->$method($config, $selected, $custom);
        } elseif ($method === 'makeRelationship') {
            $inputString = $this->htmlGenerator->makeRelationship(
                $config,
                $this->inputCalibrator->getField($config, 'label', 'name'),
                $this->inputCalibrator->getField($config, 'value', 'id'),
                $custom
            );
        } else {
            $config = $this->prepareType($config);
            $inputString = $this->htmlGenerator->makeHTMLInputString($config);
        }

        return $inputString;
    }

    /**
     * prepare the type.
     *
     * @param array $config
     *
     * @return array
     */
    public function prepareType($config)
    {
        $config['type'] = $config['inputTypes']['string'];

        if (isset($config['inputTypes'][$config['inputType']])) {
            $config['type'] = $config['inputTypes'][$config['inputType']];
        }

        return $config;
    }

    /**
     * prepare the object Value.
     *
     * @param array $config
     *
     * @return array
     */
    public function prepareObjectValue($config)
    {
        if (is_array($config['objectValue'])) {
            $config['objectValue'] = json_encode($config['objectValue']);
        }

        return $config;
    }

    /**
     * Get the generator method.
     *
     * @param string $type
     *
     * @return string
     */
    public function getGeneratorMethod($type)
    {
        switch ($type) {
            case in_array($type, $this->inputGroups['hidden']):
                return 'makeHidden';

            case in_array($type, $this->inputGroups['text']):
                return 'makeText';

            case in_array($type, $this->inputGroups['select']):
                return 'makeSelected';

            case in_array($type, $this->inputGroups['checkbox']):
                return 'makeCheckbox';

            case in_array($type, $this->inputGroups['radio']):
                return 'makeRadio';

            case in_array($type, $this->inputGroups['relationship']):
                return 'makeRelationship';

            default:
                return 'makeHTMLInputString';
        }
    }
}
