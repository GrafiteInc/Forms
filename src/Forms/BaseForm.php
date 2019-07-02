<?php

namespace Grafite\FormMaker\Forms;

use Exception;
use Grafite\FormMaker\Forms\Form;
use Grafite\FormMaker\Builders\FieldBuilder;
use Grafite\FormMaker\Services\FormMaker;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\UrlGenerator;

class BaseForm extends Form
{
    /**
     * The form route
     *
     * @var string
     */
    public $route;

    /**
     * The form method
     *
     * @var string
     */
    public $method = 'post';

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
     * Form button words
     *
     * @var array
     */
    public $buttons = [
        'save' => 'Save',
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
        'save' => 'btn btn-primary',
        'cancel' => 'btn btn-secondary',
        'delete' => 'btn btn-danger',
    ];

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
     * Constructor
     *
     * @param UrlGenerator $url
     * @param Factory $view
     * @param Request $request
     * @param FieldBuilder $fieldBuilder
     */
    public function __construct(
        UrlGenerator $url,
        Factory $view,
        FieldBuilder $fieldBuilder
    ) {
        $this->url = $url;
        $this->view = $view;
        $this->field = $fieldBuilder;
        $this->session = session();
        $this->fields = $this->fields();

        if (empty($this->fields)) {
            throw new Exception("Invalid fields", 1);
        }

        $this->builder = app(FormMaker::class);

        if (is_null($this->buttonLinks['cancel'])) {
            $this->buttonLinks['cancel'] = url()->current();
        }

        if (!is_null($this->orientation)) {
            $this->builder->setOrientation($this->orientation);
        }
    }

    /**
     * Create a form
     *
     * @return \Grafite\FormMaker\Forms\BaseForm
     */
    public function make()
    {
        $formClass = 'form';

        if ($this->orientation == 'horizontal') {
            $formClass = 'form-horizontal';
        }

        $this->html = $this->open([
            'route' => [
                $this->route
            ],
            'method' => $this->method,
            'files' => $this->hasFiles,
            'class' => $formClass,
        ]);

        $fields = $this->parseFields($this->fields());

        $this->html .= $this->builder
            ->setColumns($this->columns)
            ->fromFields($fields);

        $this->html .= $this->formButtonsAndClose();

        return $this;
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

        $lastRowInForm .= $this->field->submit($this->buttons['save'], [
            'class' => 'btn btn-primary'
        ]);

        $lastRowInForm .= '</div></div>'.$this->close();

        return $lastRowInForm;
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
