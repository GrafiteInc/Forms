<?php

namespace Grafite\FormMaker\Builders;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

class FieldBuilder
{
    /**
     * Consider Request variables while auto fill.
     * @var bool
     */
    protected $considerRequest = false;

    /**
     * Session
     *
     * @var \Illuminate\Contracts\Session\Session
     */
    public $session;

    /**
     * The types of inputs to not fill values on by default.
     *
     * @var array
     */
    protected $skipValueTypes = [
        'file',
        'password',
        'checkbox',
        'radio'
    ];

    /**
     * Input Type.
     *
     * @var null
     */
    protected $type = null;

    /**
     * An array of label names we've created.
     *
     * @var array
     */
    protected $labels = [];

    /**
     * onclick message
     *
     * @var string
     */
    protected $onclickMessage;

    /**
     * Create a new form builder instance.
     *
     * @param  \Illuminate\Contracts\View\Factory $view
     * @param  Request $request
     */
    public function __construct(Factory $view, Request $request = null)
    {
        $this->view = $view;
        $this->request = $request;
        $this->session = $request->session();
    }

    /**
     * Create a form input field.
     *
     * @param  string $type
     * @param  string $name
     * @param  string $value
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function input($type, $name, $value = null, $options = [])
    {
        $this->type = $type;

        if (! isset($options['name'])) {
            $options['name'] = $name;
        }

        // We will get the appropriate value for the given field. We will look for the
        // value in the session for the value in the old input data then we'll look
        // in the model instance if one is set. Otherwise we will just use empty.
        $id = $this->getIdAttribute($name, $options);

        if (! in_array($type, $this->skipValueTypes)) {
            $value = $this->getValueAttribute($name, $value);
        }

        // Once we have the type, value, and ID we can merge them into the rest of the
        // attributes array so we can convert them into their HTML attribute format
        // when creating the HTML element. Then, we will return the entire input.
        $merge = compact('type', 'value', 'id');

        $options = array_merge($options, $merge);

        return $this->toHtmlString('<input' . $this->attributes($options) . '>');
    }

    /**
     * Get a value from the session's old input.
     *
     * @param  string $name
     *
     * @return mixed
     */
    public function old($name)
    {
        if (isset($this->session)) {
            $key = $this->transformKey($name);
            $payload = $this->session->getOldInput($key);

            if (!is_array($payload)) {
                return $payload;
            }

            if (!in_array($this->type, ['select', 'checkbox'])) {
                if (!isset($this->payload[$key])) {
                    $this->payload[$key] = collect($payload);
                }

                if (!empty($this->payload[$key])) {
                    $value = $this->payload[$key]->shift();
                    return $value;
                }
            }

            return $payload;
        }
    }

/**
     * Transform key from array to dot syntax.
     *
     * @param  string $key
     *
     * @return mixed
     */
    protected function transformKey($key)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }

    /**
     * Determine if the old input is empty.
     *
     * @return bool
     */
    public function oldInputIsEmpty()
    {
        return (isset($this->session) && count((array) $this->session->getOldInput()) === 0);
    }

    /**
     * Get the model value that should be assigned to the field.
     *
     * @param  string $name
     *
     * @return mixed
     */
    public function getModelValueAttribute($name)
    {
        $key = $this->transformKey($name);

        if (method_exists($this->model, 'getFormValue')) {
            return $this->model->getFormValue($key);
        }

        return data_get($this->model, $this->transformKey($name));
    }

    /**
     * Take Request in fill process
     *
     * @param bool $consider
     */
    public function considerRequest($consider = true)
    {
        $this->considerRequest = $consider;
    }

    /**
     * Get the ID attribute for a field name.
     *
     * @param  string $name
     * @param  array  $attributes
     *
     * @return string
     */
    public function getIdAttribute($name, $attributes)
    {
        if (array_key_exists('id', $attributes)) {
            return $attributes['id'];
        }

        if (in_array($name, $this->labels)) {
            return $name;
        }
    }

    /**
     * Determine if an array is associative.
     *
     * @param  array $array
     * @return bool
     */
    public function isAssociativeArray($array)
    {
        return (array_values($array) !== $array);
    }
    /**
     * Determine if the provide value loosely compares to the value assigned to the field.
     * Use loose comparison because Laravel model casting may be in affect and therefore
     * 1 == true and 0 == false.
     *
     * @param  string $name
     * @param  string $value
     * @return bool
     */
    public function compareValues($name, $value)
    {
        return $this->getValueAttribute($name) == $value;
    }

    /**
     * Determine if old input or model input exists for a key.
     *
     * @param  string $name
     *
     * @return bool
     */
    public function missingOldAndModel($name)
    {
        return (is_null($this->old($name)) && is_null($this->getModelValueAttribute($name)));
    }

    /**
     * Determine if the value is selected.
     *
     * @param  string $value
     * @param  string $selected
     *
     * @return null|string
     */
    public function getSelectedValue($value, $selected)
    {
        if (is_array($selected)) {
            return in_array($value, $selected, true) || in_array((string) $value, $selected, true) ? 'selected' : null;
        } elseif ($selected instanceof Collection) {
            return $selected->contains($value) ? 'selected' : null;
        }
        if (is_int($value) && is_bool($selected)) {
            return (bool)$value === $selected;
        }
        return ((string) $value === (string) $selected) ? 'selected' : null;
    }

    /**
     * Get value from current Request
     *
     * @param $name
     *
     * @return array|null|string
     */
    public function request($name)
    {
        if (!$this->considerRequest) {
            return null;
        }

        if (!isset($this->request)) {
            return null;
        }

        return $this->request->input($this->transformKey($name));
    }

    public function getValueAttribute($name, $value = null)
    {
        if (is_null($name)) {
            return $value;
        }

        $old = $this->old($name);

        if (! is_null($old) && $name !== '_method') {
            return $old;
        }

        if (function_exists('app')) {
            $hasNullMiddleware = app("Illuminate\Contracts\Http\Kernel")
                ->hasMiddleware(ConvertEmptyStringsToNull::class);

            if ($hasNullMiddleware
                && is_null($old)
                && is_null($value)
                && !is_null($this->view->shared('errors'))
                && count($this->view->shared('errors')) > 0
            ) {
                return null;
            }
        }

        $request = $this->request($name);
        if (! is_null($request) && $name != '_method') {
            return $request;
        }

        if (! is_null($value)) {
            return $value;
        }

        if (isset($this->model)) {
            return $this->getModelValueAttribute($name);
        }
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
        // For numeric keys we will assume that the value is a boolean attribute
        // where the presence of the attribute represents a true value and the
        // absence represents a false value.
        // This will convert HTML attributes such as "required" to a correct
        // form instead of using incorrect numerics.
        if (is_numeric($key)) {
            return $value;
        }

        // Treat boolean attributes as HTML properties
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
     * Transform the string to an Html serializable object
     *
     * @param $html
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function toHtmlString($html)
    {
        return new HtmlString($html);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return \Illuminate\Contracts\View\View|mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasComponent($method)) {
            return $this->componentCall($method, $parameters);
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }
}
