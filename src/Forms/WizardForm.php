<?php

namespace Grafite\Forms\Forms;

use Illuminate\Routing\UrlGenerator;
use Grafite\Forms\Services\FormMaker;
use Grafite\Forms\Builders\FieldBuilder;

class WizardForm extends HtmlForm
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
     * The column style for the form
     *
     * @var string
     */
    public $columns = 'steps';

    /**
     * Whether or not the content should validate on keydown
     *
     * @var boolean
     */
    public $withJsValidation = true;

    /**
     * Progress bar color
     *
     * @var string
     */
    public $progressBarColor;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->url = app(UrlGenerator::class);
        $this->field = app(FieldBuilder::class);
        $this->session = session();

        $this->builder = app(FormMaker::class);

        if (is_null($this->buttonLinks['cancel'])) {
            $this->buttonLinks['cancel'] = request()->fullUrl();
        }

        $this->buttonClasses['submit'] = 'form-submit-btn ' . config('forms.buttons.submit', 'btn btn-primary');

        if (! is_null($this->orientation)) {
            $this->builder->setOrientation($this->orientation);
        }

        if (! is_null($this->withJsValidation)) {
            $this->builder->setJsValidation($this->withJsValidation);
        }
    }

    /**
     * Set the route
     *
     * @param string $name
     * @param mixed $parameters
     *
     * @return \Grafite\Forms\Forms\BaseForm
     */
    public function setRoute($name, $parameters = [])
    {
        if (is_array($parameters)) {
            $this->route = array_merge([ $name ], $parameters);
        } else {
            $this->route = [
                $name,
                $parameters,
            ];
        }

        return $this;
    }

    /**
     * Create a form
     *
     * @return \Grafite\Forms\Forms\BaseForm
     */
    public function make()
    {
        if ($this->orientation == 'horizontal') {
            if ($this->formClass === config('forms.form.horizontal-class')) {
                $this->formClass = config('forms.form.horizontal-class', 'form-horizontal');
            }
        }

        $this->builder->setSteps($this->steps());

        $options = [
            'route' => $this->route,
            'method' => $this->method,
            'files' => $this->hasFiles,
            'class' => $this->formClass,
            'id' => $this->formId,
        ];

        if ($this->withLivewire) {
            $options['wire:submit.prevent'] = 'submit';
        }

        $this->html = $this->open($options);

        $fields = $this->parseFields($this->fields());

        $this->renderedFields = $this->builder
            ->setColumns($this->columns)
            ->setLivewire($this->withLivewire)
            ->setLivewireOnKeydown($this->livewireOnKeydown)
            ->setErrorBag($this->errorBag)
            ->setFormJs($this->scripts())
            ->setFormStyles($this->styles())
            ->fromFields($fields);

        if ($this->isCardForm) {
            $cardBody = config('forms.form.cards.card-body', 'card-body');
            $this->html .= "<div class=\"{$cardBody}\">";
        }

        $this->html .= $this->stepIndicators();

        $this->html .= $this->renderedFields;

        if ($this->isCardForm) {
            $this->html .= '</div>';
        }

        $this->html .= $this->formButtonsAndClose();

        return $this;
    }

    public function stepIndicators()
    {
        $html = '<div class="form-progress-bar">';

        foreach (array_keys($this->steps()) as $key => $title) {
            $htmlTitle = (is_numeric($title)) ? '' : 'title="' . $title . '"';
            $html .= '<div class="form-step">';
            $html .= '<span ' . $htmlTitle . ' class="form-bullet" data-bullet="' . $key . '">' . ($key + 1) . '</span>';
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    public function steps()
    {
        return [];
    }

    public function scripts()
    {
        return <<<EOT
window.Form_steps = function () {
    let _steps = document.querySelectorAll('.form_step');

    window.location.hash = '1';

    window.Form_show_step(1)
};

window.Form_show_step = function (stepToShow, validate) {
    let _steps = document.querySelectorAll('.form_step');
    let _submitButton = _steps[0].closest('form').querySelector('.form-submit-btn');

    _steps.forEach(function (step) {
        step.classList.add('d-none');
        if (step.getAttribute('data-step') == stepToShow) {
            step.classList.remove('d-none');
        }
    });

    _submitButton.classList.add('d-none');
    document.querySelector('.form-previous-btn').closest('.d-flex')
        .classList.remove('justify-content-end');
    document.querySelector('.form-previous-btn').closest('.d-flex')
        .classList.add('justify-content-between');

    if (stepToShow > 1) {
         document.querySelector('.form-previous-btn').classList.remove('d-none');
    }

    if (stepToShow == 1) {
         document.querySelector('.form-previous-btn').classList.add('d-none');
         document.querySelector('.form-previous-btn').closest('.d-flex')
            .classList.remove('justify-content-between');
         document.querySelector('.form-previous-btn').closest('.d-flex')
            .classList.add('justify-content-end');
    }

    if (stepToShow < _steps.length) {
         document.querySelector('.form-next-btn').classList.remove('d-none');
    }

    if (stepToShow == _steps.length) {
         document.querySelector('.form-next-btn').classList.add('d-none');
         _submitButton.classList.remove('d-none');
    }
};

window.Form_next_step = function () {
    let _validCount = 0;
    let _count = document.querySelectorAll('.form_step').length;
    let _step = parseInt(window.location.hash.substr(1));

    if (_step < _count) {
        let _inputs = document.querySelector('[data-step="' + _step + '"]').querySelectorAll('input');
        let _selects = document.querySelector('[data-step="' + _step + '"]').querySelectorAll('select');
        let _textarea = document.querySelector('[data-step="' + _step + '"]').querySelectorAll('textarea');
        let _inputFields = [..._inputs].concat([..._selects]).concat([..._textarea]);

        _inputFields.forEach(function (_input) {
            if (! _input.validity.patternMismatch
                && ! _input.validity.valueMissing
                && ! _input.validity.tooShort
                && ! _input.validity.tooLong
                && ! _input.validity.badInput) {
                    _validCount++;
                }
                else {
                    if (! _input.classList.contains('is-invalid')) {
                        let _errorMessage = document.createElement('div');
                        _errorMessage.classList.add('invalid-feedback');
                        _errorMessage.innerText = _input.validationMessage;

                        _input.classList.add('is-invalid');
                        _input.parentNode.appendChild(_errorMessage);
                        window.Forms_validation();
                    }
                }
        });

        if (_validCount == _inputFields.length) {
            window.location.hash = _step + 1;
            window.Form_show_step(_step + 1, true);
            let _previous = _step - 1;
            document.querySelector('[data-bullet="' + _previous + '"]').classList.add('completed');
        }
    }

    if (_step == _count) {
        document.querySelector('.form-next-btn').classList.add('d-none');
    }
};

window.Form_previous_step = function () {
    let _count = document.querySelectorAll('.form_step').length;
    let _step = parseInt(window.location.hash.substr(1));

    if (_step != 1) {
        window.location.hash = _step - 1;
        window.Form_show_step(_step - 1)
        let _previous = _step - 2;
        document.querySelector('[data-bullet="' + _previous + '"]').classList.remove('completed');
    }

    if (_step == 1) {
        document.querySelector('.form-previous-btn').classList.add('d-none');
    }
};

window.Form_steps();
EOT;
    }

    public function styles()
    {
        $color = $this->progressBarColor ?? '#28a745';
        $numberOfSteps = count($this->steps());
        $size = $numberOfSteps * 100;
        $width = ($size - ($numberOfSteps * 15)) / $numberOfSteps;

        return <<<EOT
.form-progress-bar  {
	display: flex;
	justify-content: space-between;
	align-items: flex-end;
	width: ${size}px;
	margin:  0 auto;
	margin-bottom: 24px;
}

.form-step  {
    text-align: center;
}

.form-step-text  {
    margin-bottom: 10px;
    color: ${color};
    display: block;
}

.form-bullet {
	border: 1px solid ${color};
	height: 24px;
	width: 24px;
	border-radius: 100%;
	color: ${color};
	display: inline-block;
	position: relative;
	transition: background-color 500ms;
    line-height: 20px;
    cursor: pointer;
}

.form-bullet.completed  {
	color:  white;
	background-color: ${color};
}

.form-bullet.completed::after {
	content: '';
	position: absolute;
	left: 30px;
	bottom: 10px;
	height: 1px;
	width: ${width}px;
	background-color: ${color};
}
EOT;
    }
}
