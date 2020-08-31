<?php

namespace Grafite\Forms\Forms\Traits;

trait VueComponent
{
    public function vueData()
    {
        $instance = $this->getInstance();

        $accessibleFields = array_keys($this->parseFields($this->fields()));

        return collect($instance->getAttributes())->filter(function ($value, $key) use ($accessibleFields) {
            return in_array($key, $accessibleFields);
        });
    }

    public function vueSettings()
    {
        return collect([
            'buttons' => $this->buttons,
            'buttonClasses' => $this->buttonClasses,
        ]);
    }

    public function vueFields()
    {
        $fields = $this->parseFields($this->fields());

        return collect($fields)->map(function ($fieldConfig, $field) {
            return app(FieldMaker::class)->forVue($field, $fieldConfig, null);
        });
    }
}
