<?php

namespace Grafite\Forms\Forms;

use Exception;
use Illuminate\Routing\UrlGenerator;
use Grafite\Forms\Services\FormMaker;
use Grafite\Forms\Builders\FieldBuilder;

class HtmlForm extends Form
{
    /**
     * Simple wrapper for cards
     *
     * @var boolean
     */
    public $isCardForm = false;

    /**
     * Override the right alignment of buttons
     *
     * @var boolean
     */
    public $buttonsJustified = false;

    /**
     * Hide the forms buttons
     * this is only needed, when we do form submissions by other events.
     *
     * @var boolean
     */
    public $hideButtons = false;

    /**
     * Disable buttons after submit
     *
     * @var boolean
     */
    public $disableOnSubmit = false;

    /**
     * The form orientation
     *
     * @var string
     */
    public $orientation;

    /**
     * The form validation
     *
     * @var boolean
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
     * @var mixed
     */
    public $columns = 1;

    /**
     * Maximum columns in a row
     *
     * @var integer
     */
    public $maxColumns = 6;

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
     * A string context if you wish to confirm the form submission
     *
     * @var string
     */
    public $confirmSubmission = null;

    /**
     * If the submit should occur on keydown
     *
     * @var boolean
     */
    public $submitOnKeydown = false;

    /**
     * If the submit should occur on change
     *
     * @var boolean
     */
    public $submitOnChange = false;

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
     * Restrict the fields to only those in the only list.
     *
     * @var array
     */
    public $only = [];

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
     * The form builder
     *
     * @var \Grafite\Forms\Services\FormMaker
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
        'confirm' => 'Confirm',
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
     * Form button onclick actions
     *
     * @var array
     */
    public $buttonActions = [];

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
        'confirm' => null,
    ];

    public function __construct()
    {
        parent::__construct();

        $this->url = app(UrlGenerator::class);
        $this->field = app(FieldBuilder::class);
        $this->session = session();

        $this->builder = app(FormMaker::class);

        if (! is_null($this->orientation)) {
            $this->builder->setOrientation($this->orientation);
        }

        if (! is_null($this->withJsValidation)) {
            $this->builder->setJsValidation($this->withJsValidation);
        }

        $buttonClasses = [
            'submit' => $this->buttonClasses['submit'] ?? config('forms.buttons.submit', 'btn btn-primary'),
            'edit' => $this->buttonClasses['edit'] ?? config('forms.buttons.edit', 'btn btn-outline-primary'),
            'delete' => $this->buttonClasses['delete'] ?? config('forms.buttons.delete', 'btn btn-danger'),
            'cancel' => $this->buttonClasses['cancel'] ?? config('forms.buttons.cancel', 'btn btn-secondary'),
            'confirm' => $this->buttonClasses['confirm'] ?? config('forms.buttons.confirm', 'btn btn-outline-primary'),
            'next' => $this->buttonClasses['next'] ?? config('forms.buttons.next', 'btn btn-outline-primary'),
            'previous' => $this->buttonClasses['previous'] ?? config('forms.buttons.previous', 'btn btn-outline-secondary'),
        ];

        $submitButton = (collect($this->buttons)->contains('submit')) ? 'Submit' : null;
        $deleteButton = 'Delete';
        $confirmButton = 'Confirm';
        $nextButton = 'Next';
        $previousButton = 'Previous';

        $buttons = [
            'submit' => $this->buttons['submit'] ?? $submitButton,
            'edit' => $this->buttons['edit'] ?? null,
            'cancel' => $this->buttons['cancel'] ?? null,
            'confirm' => $this->buttons['confirm'] ?? $confirmButton,
            'delete' => $this->buttons['delete'] ?? $deleteButton,
            'next' => $this->buttons['next'] ?? $nextButton,
            'previous' => $this->buttons['previous'] ?? $previousButton,
        ];

        $this->buttonClasses = array_merge($buttonClasses, $this->getExtraButtonClasses());
        $this->buttons = array_merge($buttons, $this->getExtraButtons());

        $this->formId = $this->getFormId();
        $this->formClass = $this->formClass ?? config('forms.form.class', 'form');
        $this->formDeleteClass = $this->formDeleteClass ?? config('forms.form.delete-class', 'form-inline');

        if (! isset($this->buttonLinks['cancel']) || is_null($this->buttonLinks['cancel'])) {
            $this->buttonLinks['cancel'] = request()->fullUrl();
        }

        if (! is_null($this->buttons())) {
            $this->buttons = $this->buttons();
        }
    }

    /**
     * Append to the html the close form with buttons
     *
     * @return string
     */
    protected function formButtonsAndClose()
    {
        $rowAlignment = config('forms.form.sections.row-alignment-end', 'd-flex justify-content-end');

        if (isset($this->buttons['cancel']) || $this->columns === 'steps' || $this->buttonsJustified) {
            $rowAlignment = config('forms.form.sections.row-alignment-between', 'd-flex justify-content-between');
        }

        $lastRowInForm = '';

        $formButtonRow = config('forms.form.sections.button-row', 'row');
        $formButtonColumn = config('forms.form.sections.button-column', 'col-md-12');

        if (! $this->formIsDisabled) {
            if ($this->isCardForm) {
                $cardFooter = config('forms.form.cards.card-footer', 'card-footer');
                $lastRowInForm .= "<div class=\"{$cardFooter}\">";
            }

            $lastRowInForm .= '<div class="' . $formButtonRow . '">
            <div class="' . $formButtonColumn . ' ' . $rowAlignment . '">';

            foreach ($this->getExtraButtons() as $button => $buttonText) {
                if (isset($this->buttonLinks[$button])) {
                    $lastRowInForm .= '<a class="' . $this->buttonClasses[$button]
                        . '" href="' . url($this->buttonLinks[$button]) . '">' . $this->buttons[$button] . '</a>';
                }

                if (isset($this->buttonActions[$button])) {
                    $lastRowInForm .= '<button class="' . $this->buttonClasses[$button]
                        . '" data-formsjs-onclick="' . $this->buttonActions[$button] . '">' . $this->buttons[$button] . '</button>';
                }
            }

            if (isset($this->buttons['cancel'])) {
                $lastRowInForm .= '<a class="' . $this->buttonClasses['cancel']
                    . '" href="' . url($this->buttonLinks['cancel']) . '">' . $this->buttons['cancel'] . '</a>';
            }

            $lastRowInForm .= $this->formSubmitHtml();

            $lastRowInForm .= '</div></div>';

            if ($this->isCardForm) {
                $lastRowInForm .= '</div>';
            }
        }

        if ($this->hideButtons) {
            $lastRowInForm = '';
        }

        $lastRowInForm .= $this->close();

        return $lastRowInForm;
    }

    /**
     * Set the form sections
     *
     * @return array
     */
    public function setSections($fields)
    {
        return [array_keys($fields)];
    }

    /**
     * Set the form steps
     *
     * @return array
     */
    public function steps($fields)
    {
        return [array_keys($fields)];
    }

    /**
     * Set the form options
     *
     * @param array $values
     * @return self
     */
    public function setOptions($values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Set the fields
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }

    /**
     * Get custom buttons
     *
     * @return array
     */
    public function getExtraButtons()
    {
        return collect($this->buttons)->filter(function ($buttonText, $button) {
            return ! in_array($button, ['confirm', 'cancel', 'submit', 'delete', 'edit', 'next', 'previous']);
        })->toArray();
    }

    /**
     * Get custom button classes
     *
     * @return array
     */
    public function getExtraButtonClasses()
    {
        return collect($this->buttonClasses)->filter(function ($buttonText, $button) {
            return ! in_array($button, ['cancel', 'submit', 'delete', 'edit', 'next', 'previous']);
        })->toArray();
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
            throw new Exception('Invalid fields', 1);
        }

        if (empty($this->only)) {
            $this->only = collect($formFields)->map(function ($config) {
                return $config->name;
            })->toArray();
        }

        foreach ($formFields as $fieldConfig) {
            $config = $fieldConfig->toArray();

            $key = $fieldConfig->name;

            if ($this->formIsDisabled) {
                $config['attributes']['disabled'] = 'disabled';
            }

            unset($config['name']);

            if (in_array($key, $this->only)) {
                $fields[$key] = $config;
            }
        }

        return $fields;
    }

    protected function formSubmitHtml()
    {
        $html = '';
        $onSubmit = null;

        if ($this->disableOnSubmit) {
            $processing = '<i class="spinner-border spinner-border-sm"></i>';
            $onSubmit = "return window.FormsJS_validate_submission(this.form, {$processing});";
        }

        if ($this->columns === 'steps') {
            $html .= $this->field->button($this->buttons['previous'], [
                'class' => $this->buttonClasses['previous'] . ' form-previous-btn',
                'data-formsjs-onclick' => 'window.FormJS_previous_step(event)',
            ]);
            $html .= $this->field->button($this->buttons['next'], [
                'class' => $this->buttonClasses['next'] . ' form-next-btn',
                'data-formsjs-onclick' => 'window.FormJS_next_step(event)',
            ]);
        }

        $submitMethod = is_null($this->submitMethod) ? $onSubmit : "{$this->submitMethod}(event)";
        $submitType = is_null($this->submitMethod) ? 'submit' : 'button';
        $submitConfirmation = ! is_null($this->confirmSubmission) ? [
            'data-formsjs-confirm-message' => $this->confirmSubmission,
        ] : [];

        if (isset($this->buttons['submit'])) {
            $html .= $this->field->button($this->buttons['submit'], array_merge([
                'class' => $this->buttonClasses['submit'],
                'type' => $submitType,
                'data-formsjs-onclick' => $submitMethod,
            ], $submitConfirmation));
        }

        if (
            isset($this->buttons)
            && isset($this->buttons[0])
            && get_class($this->buttons[0]) === 'Grafite\Forms\Services\HtmlConfigProcessor'
        ) {
            foreach ($this->buttons as $button) {
                $html .= (string) $button;
            }
        }

        return $html;
    }

    /**
     * Scripts for the Form
     *
     * @return mixed
     */
    public function scripts()
    {
        return null;
    }

    /**
     * Styles for the Form
     *
     * @return mixed
     */
    public function styles()
    {
        return null;
    }

    /**
     * Outline the buttons for the form
     *
     * @return mixed
     */
    public function buttons()
    {
        return null;
    }

    /**
     * Set the form as a card style.
     *
     * @return void
     */
    public function asCard()
    {
        $this->isCardForm = true;

        return $this;
    }

    /**
     * Make the form columns responsive to the field count.
     *
     * @return void
     */
    public function responsive()
    {
        $this->columns = count($this->fields());

        return $this;
    }

    public function hideButtons()
    {
        $this->hideButtons = true;

        return $this;
    }

    public function hideButtonsWhen($callback)
    {
        $result = $callback();

        if ($result) {
            $this->hideButtons = true;
        }

        return $this;
    }

    /**
     * Set the disable on submit to true.
     *
     * @return void
     */
    public function disableOnSubmit()
    {
        $this->disableOnSubmit = true;

        return $this;
    }

    /**
     * Set the orientation to horizontal.
     *
     * @return void
     */
    public function horizontal()
    {
        $this->orientation = 'horizontal';

        return $this;
    }

    /**
     * Set the html to the rendered fields
     *
     * @return self
     */
    public function renderedFields()
    {
        $this->html = $this->renderedFields;

        return $this;
    }

    /**
     * Set the form to submit via ajax.
     *
     * @return self
     */
    public function viaAjax()
    {
        $this->submitViaAjax = true;

        return $this;
    }

    /**
     * Set the confirmation message for delete forms
     *
     * @param string $message
     * @param string $method
     *
     * @return self
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
     * @return \Grafite\Forms\Forms\HtmlForm
     */
    public function disable()
    {
        $this->formIsDisabled = true;

        return $this;
    }

    /**
     * Restrict a form to only the fields listed here.
     *
     * @param array $fields
     * @return self
     */
    public function only($fields)
    {
        if (! is_array($fields)) {
            $fields = [$fields];
        }

        $this->only = $fields;

        return $this;
    }

    /**
     * Set a form as disabled to prevent submission, when callback is true.
     *
     * @return self
     */
    public function disabledWhen($callback)
    {
        $result = $callback();

        if ($result) {
            $this->formIsDisabled = true;
        }

        return $this;
    }
}
