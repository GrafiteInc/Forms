<?php

namespace Grafite\FormMaker\Forms;

use Grafite\FormMaker\Forms\Form;

class HtmlForm extends Form
{
    /**
     * The form orientation
     *
     * @var string
     */
    public $orientation;

    /**
     * Number of columns for the form
     *
     * @var integer
     */
    public $columns = 1;

    /**
     * Whether or not the form has files
     *
     * @var boolean
     */
    public $hasFiles = false;

    /**
     * The route prefix, generally single form of model
     *
     * @var string

     * Form fields as array
     *
     * @var array
     */
    public $fields = [];

    /**
     * Html string for output
     *
     * @var string
     */
    protected $html;

    /**
     * Message for delete confirmation
     *
     * @var string
     */
    public $confirmMessage;

    /**
     * The field builder
     *
     * @var \Grafite\FormMaker\Builders\FieldBuilder
     */
    protected $builder;

    /**
     * Form button words
     *
     * @var array
     */
    public $buttons = [
        'submit' => 'Submit',
        'cancel' => 'Cancel',
    ];

    /**
     * Form button links
     *
     * @var array
     */
    public $buttonLinks = [
        'cancel' => null,
    ];

    /**
     * Form button classes
     *
     * @var array
     */
    public $buttonClasses = [
        'submit' => 'btn btn-primary',
        'cancel' => 'btn btn-secondary',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Append to the html the close form with buttons
     *
     * @return string
     */
    protected function formButtonsAndClose()
    {
        $flexAlignment = (isset($this->buttons['cancel'])) ? 'between' : 'end';

        $lastRowInForm = '<div class="row"><div class="col-md-12 d-flex justify-content-'.$flexAlignment.'">';

        if (isset($this->buttons['cancel'])) {
            $lastRowInForm .= '<a class="'.$this->buttonClasses['cancel']
                .'" href="'.url($this->buttonLinks['cancel']).'">'.$this->buttons['cancel'].'</a>';
        }

        $lastRowInForm .= $this->field->submit($this->buttons['submit'], [
            'class' => 'btn btn-primary'
        ]);

        $lastRowInForm .= '</div></div>'.$this->close();

        return $lastRowInForm;
    }

    /**
     * Set the form sections
     *
     * @return array
     */
    public function setSections()
    {
        return [array_keys($this->parseFields($this->fields()))];
    }

    /**
     * Set the confirmation message for delete forms
     *
     * @param string $message
     *
     * @return \Grafite\FormMaker\Forms\ModelForm
     */
    public function confirm($message)
    {
        $this->confirmMessage = $message;

        return $this;
    }

    /**
     * Set the fields
     *
     * @return \Grafite\FormMaker\Forms\ModelForm
     */
    public function fields()
    {
        return [];
    }

    /**
     * Parse the fields to get proper config
     *
     * @param array $formFields
     *
     * @return array
     */
    protected function parseFields($formFields)
    {
        $fields = [];

        foreach ($formFields as $config) {
            $key = array_key_first($config);
            $fields[$key] = $config[$key];
        }

        return $fields;
    }

    /**
     * Output html as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->html;
    }
}
