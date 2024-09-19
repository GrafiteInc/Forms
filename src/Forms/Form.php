<?php

namespace Grafite\Forms\Forms;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\Routing\UrlGenerator;
use Grafite\Forms\Services\FormMaker;
use Grafite\Forms\Traits\HasErrorBag;
use Grafite\Forms\Builders\FieldBuilder;
use Grafite\Forms\Builders\AttributeBuilder;
use Grafite\Forms\Services\FormAssets;

class Form
{
    use HasErrorBag;

    /**
     * Laravel session
     *
     * @var Session
     */
    public $session;

    /**
     * The error bag
     *
     * @var mixed
     */
    public $errorBag;

    /**
     * If the form should be submitted on keydown
     *
     * @var bool
     */
    public $onKeydown = false;

    /**
     * If the form should be submitted on change
     *
     * @var bool
     */
    public $onChange = false;

    /**
     * If we want to run the submission through an ajax call
     *
     * @var boolean
     */
    public $submitViaAjax = null;

    /**
     * If the form should be livewire based or not
     *
     * @var bool
     */
    public $withLivewire = false;

    /**
     * If the form should submit on keydown
     *
     * @var bool
     */
    public $livewireOnKeydown = false;

    /**
     * If the form should submit on change
     *
     * @var bool
     */
    public $livewireOnChange = false;

    /**
     * The model to be bound
     *
     * @var mixed
     */
    public $model;

    /**
     * The URL Generator
     *
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    public $url;

    /**
     * The Field Builder
     *
     * @var \Grafite\Forms\Builders\FieldBuilder
     */
    public $field;

    /**
     * The Asset Builder
     *
     * @var \Grafite\Forms\Services\FormAssets
     */
    public $assets;

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
     * Method for delete confirmation
     *
     * @var string
     */
    public $confirmMethod;

    /**
     * A payload for the form
     *
     * @var array
     */
    public $payload;

    /**
     * An ID for the form
     *
     * @var string|null
     */
    public $formId = null;

    /**
     * Modal message
     *
     * @var string|null
     */
    public $message = null;

    /**
     * Content for a trigger button
     *
     * @var string|null
     */
    public $triggerContent = null;

    /**
     * Class for the trigger button
     *
     * @var string|null
     */
    public $triggerClass = null;

    /**
     * Text for the modal title
     *
     * @var string|null
     */
    public $modalTitle = null;

    /**
     * The reserved form open attributes.
     *
     * @var array
     */
    protected $reserved = [
        'method',
        'url',
        'route',
        'action',
        'files',
    ];

    /**
     * The form methods that should be spoofed, in uppercase.
     *
     * @var array
     */
    protected $spoofedMethods = [
        'DELETE',
        'PATCH',
        'PUT',
    ];

    /**
     * Create a new form builder instance.
     */
    public function __construct()
    {
        $this->url = app(UrlGenerator::class);
        $this->field = app(FieldBuilder::class);
        $this->assets = app(FormAssets::class);
        $this->session = session();

        $this->config();
    }

    /**
     * This function returns nothing and is meant
     * for overriding the forms config values
     *
     * @return void
     */
    public function config()
    {
        // Return nothing please.
    }

    /**
     * This function returns nothing and is meant
     * for setting up details of the form
     *
     * @return void
     */
    public function setUp()
    {
        // Return nothing please.
    }

    /**
     * Generate a button form based on method and route
     *
     * @param string $method
     * @param string $route
     * @param string $button
     * @return self
     */
    public function action($method, $route, $button = 'Send', $options = [], $asModal = false, $disableOnSubmit = false, $submitViaAjax = false)
    {
        if (! is_null($this->submitViaAjax)) {
            $submitViaAjax = $this->submitViaAjax;
        }

        $this->html = $this->open([
            'route' => $route,
            'method' => $method,
            'class' => config('forms.form.inline-class', 'form d-inline'),
        ]);

        $options = array_merge([
            'class' => config('forms.buttons.submit', 'btn btn-primary'),
        ], $options);

        if (! empty($this->confirmMessage) && is_null($this->confirmMethod)) {
            $options = array_merge($options, [
                'data-formsjs-confirm-message' => $this->confirmMessage,
                'data-formsjs-onclick' => "FormsJS_confirm(event)",
            ]);
        }

        if (! empty($this->confirmMessage) && is_null($this->confirmMethod) && $submitViaAjax) {
            $options = array_merge($options, [
                'data-formsjs-onclick' => "FormsJS_confirmForAjax(event)",
            ]);
        }

        if (! empty($this->confirmMessage) && ! is_null($this->confirmMethod)) {
            $options = array_merge($options, [
                'data-formsjs-confirm-message' => $this->confirmMessage,
                'data-formsjs-onclick' => "{$this->confirmMethod}(event)",
            ]);
        }

        if ($disableOnSubmit && is_null($this->confirmMethod)) {
            $options = array_merge($options, [
                'data-formsjs-button' => $button,
                'data-formsjs-onclick' => 'window.FormsJS_disableOnSubmit(event)',
            ]);
        }

        $options['type'] = 'submit';

        if (! empty($this->payload)) {
            foreach ($this->payload as $key => $value) {
                $this->html .= $this->field->makeInput('hidden', $key, $value);
            }
        }

        $ajaxMethod = config('forms.global-ajax-method', 'ajax');

        $options['data-formsjs-onclick'] = ($submitViaAjax) ? $ajaxMethod . '(event)' : $options['data-formsjs-onclick'] ?? false;

        $this->html .= $this->field->button($button, $options);

        $this->html .= $this->close();

        if ($asModal) {
            $this->html = $this->asModal();
        }

        $defaultJavaScript = file_get_contents(__DIR__ . '/../JavaScript/default.js');
        $defaultJavaScript = Str::of($defaultJavaScript)->replace('_ajaxMethod', $ajaxMethod);

        $this->assets->addJs($defaultJavaScript);

        return $this;
    }

    /**
     * Get the Form ID
     *
     * @return string
     */
    public function getFormId()
    {
        if (! is_null($this->formId)) {
            return $this->formId;
        }

        return 'Form_' . Str::random(10);
    }

    /**
     * Set the payload of an action form
     *
     * @param array $values
     * @return self
     */
    public function payload($values)
    {
        $this->payload = $values;

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
     * Set the confirmation for a form as a modal
     *
     * @param string $message
     * @param string $buttonText
     * @param string $buttonClass
     *
     * @return self
     */
    public function confirmAsModal($message, $buttonText, $buttonClass = 'btn btn-primary')
    {
        $this->message = "<p class=\"mb-4\">{$message}</p>";
        $this->triggerContent = $buttonText;
        $this->triggerClass = $buttonClass;

        return $this->asModal();
    }

    /**
     * Convert to Livewire
     *
     * @return self
     */
    public function asLivewire()
    {
        $this->withLivewire = true;

        return $this;
    }

    /**
     * Set the form as an ajax based one
     *
     * @return self
     */
    public function viaAjax()
    {
        $this->submitViaAjax = true;

        return $this;
    }

    public function onKeydown()
    {
        if ($this->withLivewire) {
            $this->livewireOnKeydown = true;
        }

        $this->onKeydown = true;

        return $this;
    }

    public function onChange()
    {
        if ($this->withLivewire) {
            $this->livewireOnChange = true;
        }

        $this->onChange = true;

        return $this;
    }

    /**
     * Open up a new HTML form.
     *
     * cloned from LaravelCollective/html
     *
     * @param  array $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function open($options)
    {
        $attributes = [];
        $method = Arr::get($options, 'method', 'post');

        if (! $this->withLivewire) {
            $attributes['method'] = $this->getMethod($method);
            $attributes['action'] = $this->getAction($options);
        }

        $attributes['accept-charset'] = 'UTF-8';
        $append = '';

        if (! $this->withLivewire) {
            $append = $this->getAppendage($method);
        }

        if (isset($options['files']) && $options['files']) {
            $options['enctype'] = 'multipart/form-data';
        }

        $attributes = array_merge($attributes, Arr::except($options, $this->reserved));

        $attributes = app(AttributeBuilder::class)->render($attributes);

        return $this->toHtmlString('<form ' . $attributes . '>' . $append);
    }

    /**
     * Generate a close form tag
     *
     * cloned from LaravelCollective/html
     *
     * @return string
     */
    public function close()
    {
        return $this->toHtmlString('</form>');
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * cloned from LaravelCollective/html
     *
     * @return string
     */
    public function token()
    {
        return $this->field->makeInput('hidden', '_token', $this->session->token());
    }

    /**
     * Get the form appendage for the given method.
     *
     * cloned from LaravelCollective/html
     *
     * @param  string $method
     *
     * @return string
     */
    protected function getAppendage($method)
    {
        [$method, $appendage] = [strtoupper($method), ''];

        if (in_array($method, $this->spoofedMethods)) {
            $appendage .= $this->field->makeInput('hidden', '_method', $method);
        }

        if ($method !== 'GET') {
            $appendage .= $this->token();
        }

        return $appendage;
    }

    /**
     * Get the action for a "url" option.
     *
     * cloned from LaravelCollective/html
     *
     * @param  array|string $options
     *
     * @return string
     */
    protected function getUrlAction($options)
    {
        if (is_array($options)) {
            return $this->url->to($options[0], array_slice($options, 1));
        }

        return $this->url->to($options);
    }

    /**
     * Get the action for a "route" option.
     *
     * cloned from LaravelCollective/html
     *
     * @param  array|string $options
     *
     * @return string
     */
    protected function getRouteAction($options)
    {
        if (is_array($options)) {
            return $this->url->route($options[0], array_slice($options, 1));
        }

        return $this->url->route($options);
    }

    /**
     * Get the action for an "action" option.
     *
     * @param  array|string $options
     *
     * @return string
     */
    protected function getControllerAction($options)
    {
        if (is_array($options)) {
            return $this->url->action($options[0], array_slice($options, 1));
        }

        return $this->url->action($options);
    }

    /**
     * Get the form action from the options.
     *
     * cloned from LaravelCollective/html
     *
     * @param  array $options
     *
     * @return string
     */
    protected function getAction(array $options)
    {
        if (isset($options['url'])) {
            return $this->getUrlAction($options['url']);
        }

        if (isset($options['route']) && ! isset($options['action'])) {
            return $this->getRouteAction($options['route']);
        }

        if (isset($options['action'])) {
            return $this->getControllerAction($options['action']);
        }

        return $this->url->current();
    }

    /**
     * Create a new model based form builder.
     *
     * cloned from LaravelCollective/html
     *
     * @param  mixed $model
     * @param  array $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function model($model, array $options = [])
    {
        $this->model = $model;

        return $this->open($options);
    }

    /**
     * Set the model instance on the form builder.
     *
     * cloned from LaravelCollective/html
     *
     * @param  mixed $model
     *
     * @return void
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Get the current model instance on the form builder.
     *
     * cloned from LaravelCollective/html
     *
     * @return mixed $model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Parse the form action method.
     *
     * cloned from LaravelCollective/html
     *
     * @param  string $method
     *
     * @return string
     */
    protected function getMethod($method)
    {
        $method = strtoupper($method);

        return $method !== 'GET' ? 'POST' : $method;
    }

    /**
     * Set the values for the modal trigger
     *
     * @param string $content
     * @param string $class
     * @param string $message
     *
     * @return self
     */
    public function setModal($content, $class, $message)
    {
        $this->triggerContent = $content;
        $this->triggerClass = $class;
        $this->message = $message;

        return $this;
    }

    /**
     * Set the form id
     *
     * @param string $id
     * @return self
     */
    public function id($id)
    {
        $this->formId = $id;

        return $this;
    }

    /**
     * Set the modal title
     *
     * @param string $content
     * @return self
     */
    public function modalTitle($content)
    {
        $this->modalTitle = $content;

        return $this;
    }

    /**
     * Set the trigger css class
     *
     * @param string $cssClass
     * @return self
     */
    public function triggerClass($cssClass)
    {
        $this->triggerClass = $cssClass;

        return $this;
    }

    /**
     * Set the trigger Content
     *
     * @param string $content
     * @return self
     */
    public function triggerContent($content)
    {
        $this->triggerContent = $content;

        return $this;
    }

    /**
     * Create the form as a modal
     *
     * @return string
     */
    public function asModal($triggerContent = null, $triggerClass = null, $message = null, $modalTitle = null)
    {
        $modalTitle = $modalTitle ?? $this->modalTitle;
        $title = $modalTitle ?? 'Confirmation';

        $modalId = $this->getFormId() . '_Modal';
        $form = $this->html;
        $message = $message ?? $this->message;
        $triggerContent = $triggerContent ?? $this->triggerContent;
        $triggerClass = $triggerClass ?? $this->triggerClass;

        $closeButton = '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

        if (Str::of(config('forms.bootstrap-version'))->startsWith('5')) {
            $closeButton = '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        }

        $centered = (config('forms.modal-centered')) ? 'modal-dialog-centered' : '';

        return <<<Modal
            <div id="{$modalId}" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog {$centered} modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{$title}</h5>
                            {$closeButton}
                        </div>
                        <div class="modal-body">
                            {$message}
                            {$form}
                        </div>
                    </div>
                </div>
            </div>
            <button
                data-toggle="modal"
                data-target="#{$modalId}"
                data-bs-toggle="modal"
                data-bs-target="#{$modalId}"
                class="{$triggerClass}"
            >
                {$triggerContent}
            </button>
Modal;
    }

    /**
     * Render a single field
     *
     * @param string $field
     * @param string $name
     * @param array $options
     * @return string
     */
    public function makeField($field, $name, $options = [])
    {
        return app(FormMaker::class)->fromFields([
            app($field)->make($name, $options),
        ]);
    }

    /**
     * Transform the string to an Html serializable object
     *
     * cloned from LaravelCollective/html
     *
     * @param $html
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function toHtmlString($html)
    {
        return new HtmlString($html);
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

    /**
     * Mostly for Laravel components
     *
     * @return string
     */
    public function render()
    {
        return $this->html;
    }

    /**
     * For Livewire components
     *
     * @return string
     */
    public function renderForLivewire()
    {
        return "<div wire:ignore>{$this->html}</div>";
    }

    /**
     * Set properties
     *
     * @return self
     */
    public function setProperty($key, $value)
    {
        $this->$key = $value;

        return $this;
    }
}
