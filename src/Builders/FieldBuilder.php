<?php

namespace Grafite\Forms\Builders;

use DateTime;
use Exception;
use Grafite\Forms\Traits\HasLivewire;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class FieldBuilder
{
    use HasLivewire;

    public $withLivewire = false;

    public $livewireOnKeydown = false;

    public $livewireOnChange = false;

    public $attributeBuilder;

    public function __construct()
    {
        $this->attributeBuilder = new AttributeBuilder;
    }

    /**
     * Create a submit button element.
     *
     * @param  string  $value
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function submit($value = null, $options = [])
    {
        return $this->makeInput('submit', null, $value, $options);
    }

    /**
     * Make an html button
     *
     * @param  string  $value
     * @param  array  $options
     * @return \Illuminate\Support\HtmlString
     */
    public function button($value = null, $options = [])
    {
        if (! array_key_exists('type', $options)) {
            $options['type'] = 'button';
        }

        return '<button '.$this->attributeBuilder->render($options, null, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange).'>'.$value.'</button>';
    }

    /**
     * Make an input string
     *
     * @param  string  $type
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeInput($type, $name, $value, $options = [])
    {
        // TODO: getFieldValue()
        if ($value instanceof DateTime) {
            $value = $value->format($options['format'] ?? 'Y-m-d');
        }

        if (isset($options['value']) && is_null($value)) {
            $value = $options['value'];
            unset($options['value']);
        }

        if (in_array($type, ['file', 'password'])) {
            $value = null;
        }

        $attributes = $this->attributeBuilder->render($options, $name, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange);

        return '<input '.$attributes.' name="'.$name.'" type="'.$type.'" value="'.e($value).'">';
    }

    /**
     * Make an field string
     *
     * @param  string  $type
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeField($type, $name, $value, $options = [])
    {
        // TODO: getFieldValue()
        if ($value instanceof DateTime) {
            $value = $value->format($options['format'] ?? 'Y-m-d');
        }

        $attributes = $this->attributeBuilder->render($options, $name, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange);

        return '<'.$type.' '.$attributes.' name="'.$name.'" value="'.e($value).'"></'.$type.'>';
    }

    /**
     * Make text input.
     *
     * @param  array  $config
     * @param  string  $population
     * @param  mixed  $custom
     * @return string
     */
    public function makeCustomFile($name, $value, $options)
    {
        $labelText = $options['label'] ?? 'Choose file';

        if (
            (isset($options['multiple']) && $options['multiple']) ||
            (isset($options['attributes']['multiple']) && $options['attributes']['multiple'])
        ) {
            $name = $name.'[]';
            $labelText = ($labelText === 'Choose file') ? 'Choose files' : $labelText;
        }

        unset($options['class']);

        $fileLabel = config('forms.form.custom-file-label', 'custom-file-label');
        $customFileClass = config('forms.form.custom-file-input-class', 'custom-file-input');
        $customFileWrapperClass = config('forms.form.custom-file-wrapper-class', 'custom-file');

        $label = '<label class="'.$fileLabel.'" for="'.$options['attributes']['id'].'">'.$labelText.'</label>';
        $options['attributes']['class'] = $options['attributes']['class'].' '.$customFileClass;

        $attributes = $this->attributeBuilder->render($options['attributes'], $name, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange);

        $input = '<div class="'.$customFileWrapperClass.'">';
        $input .= '<input '.$attributes.' type="file" name="'.$name.'">';

        if (! Str::of(config('forms.bootstrap-version'))->startsWith('5')) {
            $input .= $label;
        }

        $input .= '</div>';

        return $input;
    }

    /**
     * Make a textarea.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeTextarea($name, $value, $options)
    {
        $attributes = $this->attributeBuilder->render($options['attributes'], $name, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange);

        return '<textarea '.$attributes.' name="'.$name.'">'.e($value).'</textarea>';
    }

    /**
     * Make a inline checkbox.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeCheckboxInline($name, $value, $options)
    {
        $options['check-inline'] = true;

        return $this->makeCheckbox($name, $value, $options);
    }

    /**
     * Make a inline radio.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeRadioInline($name, $value, $options)
    {
        $options['check-inline'] = true;

        return $this->makeRadio($name, $value, $options);
    }

    /**
     * Make a select.
     *
     * @param  string  $name
     * @param  mixed  $selected
     * @param  array  $options
     * @return string
     */
    public function makeSelect($name, $selected, $options)
    {
        $selectOptions = '';

        if (isset($options['attributes']['value'])) {
            $selected = $options['attributes']['value'];
            unset($options['attributes']['value']);
        }

        if (isset($options['value']) && is_null($selected)) {
            $selected = $options['value'];
        }

        if (isset($options['value']) && ! is_null($selected)) {
            $selected = $options['value'];
        }

        if (isset($options['attributes']['multiple']) && $options['attributes']['multiple']) {
            $name .= '[]';
        }

        if (
            isset($options['null_value']) && $options['null_value']
            && isset(array_values($options['options'])[0])
            && ! is_array(array_values($options['options'])[0])
        ) {
            $nullValue = [];
            $nullValue[$options['null_label'] ?? 'None'] = null;
            $options['options'] = array_merge($nullValue, $options['options']);
        }

        if (
            isset(array_values($options['options'])[0])
            && is_array(array_values($options['options'])[0])
        ) {
            if (
                ! isset($options['customOptions']['group_option_key'])
                || ! isset($options['customOptions']['group_option_value'])
            ) {
                throw new Exception("It looks like you're using option groups, you need to then set: `group_option_key` and `group_option_value` as customOptions", 1);
            }

            if (isset($options['null_value']) && $options['null_value']) {
                $nullLabel = $options['null_label'] ?? 'None';
                $options['options'] = array_merge(['Universal' => [$nullLabel => null]], $options['options']);
            }

            foreach ($options['options'] as $group => $groupOptions) {
                $label = ! empty($group) ? $group : 'Undefined';
                $selectOptions .= '<optgroup label="'.$label.'">';
                foreach ($groupOptions as $key => $value) {
                    if (is_array($value)) {
                        $key = $value[$options['customOptions']['group_option_key']];
                        $value = $value[$options['customOptions']['group_option_value']];
                    }

                    $selectedValue = $this->getOptionSelectedValue($selected, $value, $groupOptions);
                    $selectOptions .= '<option value="'.$value.'"'.$selectedValue.'>'.$key.'</option>';
                }

                $selectOptions .= '</optgroup>';
            }
        } else {
            foreach ($options['options'] as $key => $value) {
                $selectedValue = $this->getOptionSelectedValue($selected, $value, $options);
                $selectOptions .= '<option value="'.$value.'"'.$selectedValue.'>'.$key.'</option>';
            }
        }

        $attributes = $this->attributeBuilder->render($options['attributes'], $name, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange);

        return '<select '.$attributes.' name="'.$name.'">'.$selectOptions.'</select>';
    }

    /**
     * Make a datalist.
     *
     * @param  string  $name
     * @param  mixed  $selected
     * @param  array  $options
     * @return string
     */
    public function makeDatalist($name, $selected, $options)
    {
        $selectOptions = '';

        foreach ($options['options'] as $value) {
            $selectOptions .= '<option value="'.$value.'">';
        }

        $attributes = $this->attributeBuilder->render($options['attributes'], $name, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange);

        return '<input type="search" '.$attributes.' name="'.$name.'" list="'.$options['attributes']['id'].'-list"><datalist id="'.$options['attributes']['id'].'-list">'.$selectOptions.'</datalist>';
    }

    /**
     * Make a checkbox.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeCheckInput($name, $value, $options)
    {
        $customClasses = $options['attributes']['class'] ?? '';
        $customLabelClasses = $options['label_class'] ?? '';

        $options['attributes']['class'] = Str::of(config('forms.form.check-input-class', 'form-check-input').' '.$customClasses)->trim();

        if (Str::contains($options['type'], '-inline')) {
            $options['check-inline'] = true;
        }

        if (! Str::of(config('forms.bootstrap-version'))->startsWith('5') && $options['type'] === 'switch') {
            $options['attributes']['class'] = 'custom-control-input';
        }

        if (in_array($options['type'], ['radio', 'radio-inline'])) {
            $field = $this->makeRadio($name, $value, $options);
        } else {
            $field = $this->makeCheckbox($name, $value, $options);
        }

        $formClass = config('forms.form.check-class', 'form-check');

        if (isset($options['check-inline'])) {
            $formClass = config('forms.form.check-inline-class', 'form-check form-check-inline');
        }

        if ($options['type'] === 'switch') {
            $formClass = $formClass.' '.config('forms.form.check-switch-class', 'custom-switch');
        }

        $fieldWrapper = "<div class=\"{$formClass}\">";

        $label = $options['label'];

        if (! isset($options['label']) || $label === '') {
            $label = Str::title($name);
        }

        $label = str_replace('_', ' ', $label);

        if (Str::contains($label, '[')) {
            $label = $this->getNestedFieldLabel($label)[0];
        }

        $labelClass = Str::of(config('forms.form.label-check-class', 'form-check-label').' '.$customLabelClasses)->trim();

        if (! Str::of(config('forms.bootstrap-version'))->startsWith('5') && $options['type'] === 'switch') {
            $labelClass = 'custom-control-label';
        }

        $fieldLabel = "<label class=\"{$labelClass}\" for=\"{$options['attributes']['id']}\">{$label}</label>";

        return $fieldWrapper.$field.$fieldLabel.'</div>';
    }

    /**
     * Make a checkbox.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeCheckbox($name, $value, $options)
    {
        $checked = $this->isChecked($name, $value, $options);
        $attributes = $this->attributeBuilder->render($options['attributes'], $name, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange);

        return '<input '.$attributes.' type="checkbox" name="'.$name.'"'.$checked.'>';
    }

    /**
     * Make a radio.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeRadio($name, $value, $options)
    {
        $checked = $this->isChecked($name, $value, $options);
        $attributes = $this->attributeBuilder->render($options['attributes'], $name, $this->withLivewire, $this->livewireOnKeydown, $this->livewireOnChange);

        return '<input '.$attributes.' type="radio" name="'.$name.'"'.$checked.'>';
    }

    /**
     * Make a relationship input.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $options
     * @return string
     */
    public function makeRelationship($name, $value, $options)
    {
        $method = 'all';
        $class = $options['model'];

        if (! is_object($class)) {
            $class = app()->make($options['model']);
        }

        $method = $options['model_options']['method'] ?? 'all';

        // TODO this is weird
        if (! isset($options['model_options']['params'])) {
            $items = $class->$method();
        }
        if (isset($options['model_options']['params'])) {
            $items = $class->$method($options['model_options']['params']);
        }

        if (isset($options['null_value']) && $options['null_value']) {
            $options['options'][$options['null_label']] = null;
        }

        $optionLabel = $options['model_options']['label'] ?? 'name';
        $optionValue = $options['model_options']['value'] ?? 'id';

        foreach ($items as $item) {
            $options['options'][$item->$optionLabel] = $item->$optionValue;
        }

        // In case we get an Eloquent Collection or Collection
        // without specifying the ID tag which we're checking
        // the select values from - we need to set the values
        // to an array of IDs.
        if (! is_null($value) && (is_object($value) && method_exists($value, 'toArray'))) {
            $parsedValues = [];
            $optionValue = $options['model_options']['value'];

            foreach ($value->toArray() as $valueItem) {
                $parsedValues[] = $valueItem[$optionValue];
            }

            $value = $parsedValues;
        }

        return $this->makeSelect($name, $value, $options);
    }

    /**
     * Check if a field is checked
     *
     * @param  mixed  $value
     * @param  array  $options
     * @return bool
     */
    public function isChecked($name, $value, $options)
    {
        if (is_null($value) && isset($options['attributes']['value'])) {
            $value = $options['attributes']['value'];
        }

        if (! $value) {
            return '';
        }

        if (isset($options['attributes']['value']) && $value === $options['attributes']['value']) {
            return ' checked';
        }

        if (Str::contains($name, $value)) {
            return ' checked';
        }

        if ($value === true || $value === 'on' || $value === 1) {
            return ' checked';
        }

        return '';
    }

    private function getNestedFieldLabel($label)
    {
        preg_match_all("/\[([^\]]*)\]/", $label, $matches);

        return $matches[1];
    }

    protected function getOptionSelectedValue($selected, $value, $options)
    {
        $selectedValue = '';

        if (
            isset($options['attributes']['multiple'])
            && (is_object($selected) || is_array($selected))
        ) {
            if (in_array($value, collect($selected)->toArray())) {
                $selectedValue = ' selected';
            }
        }

        if (
            ! isset($options['attributes']['multiple'])
            && is_array($selected)
        ) {
            if (in_array($value, $selected)) {
                $selectedValue = ' selected';
            }
        }

        if ($selected == $value) {
            $selectedValue = ' selected';
        }

        return $selectedValue;
    }

    /**
     * Transform the string to an Html serializable object
     *
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function toHtmlString($html)
    {
        return new HtmlString($html);
    }
}
