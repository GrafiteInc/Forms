<?php

namespace Grafite\Forms\Forms;

use Exception;
use Grafite\Forms\Forms\Form;

class HtmlForm extends Form
{
    /**
     * The form orientation
     *
     * @var string
     */
    public $orientation;

    /**
     * The form validation
     *
     * @var string
     */
    public $withJsValidation = false;

    /**
     * The form class
     *
     * @var string
     */
    public $formClass;

    /**
     * The form id
     *
     * @var string
     */
    public $formId;

    /**
     * The form delete class
     *
     * @var string
     */
    public $formDeleteClass;

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
     * Whether or not the form should be disabled
     *
     * @var boolean
     */
    public $formIsDisabled = false;

    /**
     * An alternative method to perform the form submission
     *
     * @var string
     */
    public $submitMethod = null;

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
     * Html string of rendered fields
     *
     * @var string
     */
    protected $renderedFields;

    /**
     * Message for delete confirmation
     *
     * @var string
     */
    public $confirmMessage;

    /**
     * Method for delete confirmation
     *
     * @var string
     */
    public $confirmMethod;

    /**
     * The field builder
     *
     * @var \Grafite\Forms\Builders\FieldBuilder
     */
    protected $builder;

    /**
     * Form button words
     *
     * @var array
     */
    public $buttons = [
        'submit' => 'Submit',
        'edit' => 'Edit',
        'delete' => 'Delete',
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
        'submit' => null,
        'edit' => null,
        'delete' => null,
        'cancel' => null,
    ];

    public function __construct()
    {
        parent::__construct();

        $buttonClasses = [
            'submit' => $this->buttonClasses['submit'] ?? config('form-maker.buttons.submit', 'btn btn-primary'),
            'edit' => $this->buttonClasses['edit'] ?? config('form-maker.buttons.edit', 'btn btn-outline-primary'),
            'delete' => $this->buttonClasses['delete'] ?? config('form-maker.buttons.delete', 'btn btn-danger'),
            'cancel' => $this->buttonClasses['cancel'] ?? config('form-maker.buttons.cancel', 'btn btn-secondary'),
        ];

        $submitButton = (collect($this->buttons)->contains('submit')) ? 'Submit' : null;
        $deleteButton = 'Delete';

        $buttons = [
            'submit' => $this->buttons['submit'] ?? $submitButton,
            'edit' => $this->buttons['edit'] ?? null,
            'cancel' => $this->buttons['cancel'] ?? null,
            'delete' => $this->buttons['delete'] ?? $deleteButton,
        ];

        $this->buttonClasses = $buttonClasses;
        $this->buttons = $buttons;

        $this->formClass = $this->formClass ?? config('form-maker.form.class', 'form');
        $this->formDeleteClass = $this->formDeleteClass ?? config('form-maker.form.delete-class', 'form-inline');
    }

    /**
     * Append to the html the close form with buttons
     *
     * @return string
     */
    protected function formButtonsAndClose()
    {
        $rowAlignment = config('form-maker.form.sections.row-alignment-end', 'd-flex justify-content-end');

        if (isset($this->buttons['cancel'])) {
            $rowAlignment = config('form-maker.form.sections.row-alignment-between', 'd-flex justify-content-between');
        }

        $formRow = config('form-maker.form.sections.row', 'row');
        $formFullSizeColumn = config('form-maker.form.sections.full-size-column', 'col-md-12');

        $lastRowInForm = '<div class="' . $formRow . '">
            <div class="' . $formFullSizeColumn . ' ' . $rowAlignment . '">';

        if (!$this->formIsDisabled) {
            if (isset($this->buttons['cancel'])) {
                $lastRowInForm .= '<a class="' . $this->buttonClasses['cancel']
                    . '" href="' . url($this->buttonLinks['cancel']) . '">' . $this->buttons['cancel'] . '</a>';
            }

            if (!is_null($this->submitMethod)) {
                $lastRowInForm .= $this->field->button($this->buttons['submit'], [
                    'class' => $this->buttonClasses['submit'],
                    'onclick' => "{$this->submitMethod}(event)"
                ]);
            } else {
                if (isset($this->buttons['submit'])) {
                    $lastRowInForm .= $this->field->button($this->buttons['submit'], [
                        'class' => $this->buttonClasses['submit'],
                        'type' => 'submit'
                    ]);
                }
            }
        }

        $lastRowInForm .= '</div></div>' . $this->close();

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
     * @param string $method
     *
     * @return \Grafite\Forms\Forms\ModelForm
     */
    public function confirm($message, $method = null)
    {
        $this->confirmMessage = $message;
        $this->confirmMethod = $method;

        return $this;
    }

    /**
     * Set a form as disabled to prevent submission.
     *
     * @return \Grafite\Forms\Forms\ModelForm
     */
    public function disable()
    {
        $this->formIsDisabled = true;

        return $this;
    }

    /**
     * Set a form as disabled to prevent submission, when callback is true.
     *
     * @return \Grafite\Forms\Forms\ModelForm
     */
    public function disabledWhen($callback)
    {
        $result = $callback();

        if ($result) {
            $this->formIsDisabled = true;
        }

        return $this;
    }

    /**
     * Set the fields
     *
     * @return \Grafite\Forms\Forms\ModelForm
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

        if (empty($formFields)) {
            throw new Exception("Invalid fields", 1);
        }

        foreach ($formFields as $config) {
            $key = array_key_first($config);
            if ($this->formIsDisabled) {
                $config[$key]['attributes']['disabled'] = 'disabled';
            }

            $fields[$key] = $config[$key];
        }

        return $fields;
    }

    /**
     * Set the html to the rendered fields
     *
     * @return void
     */
    public function renderedFields()
    {
        $this->html = $this->renderedFields;

        return $this;
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
