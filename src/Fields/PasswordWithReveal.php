<?php

namespace Grafite\Forms\Fields;

class PasswordWithReveal extends Field
{
    protected static function getType()
    {
        return 'password';
    }

    protected static function getFactory()
    {
        return 'password';
    }

    protected static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/password-revealer@1.1.1/dist/password-revealer.min.js',
        ];
    }

    protected static function js($id, $options)
    {
        return <<<EOT
            PasswordRevealer("#${id}").init();
EOT;
    }

    protected static function getTemplate($options)
    {
        $toggle = $options['toggle'] ?? 'Toggle';
        $toggleClasses = $options['toggle-classes'] ?? 'btn btn-outline-primary bmx-rounded-left-0';

        return <<<EOT
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div class="input-group mb-3">
            {field}
            <button type="button" class="PasswordRevealer-trigger ${toggleClasses}">${toggle}</button>
        </div>
    {errors}
    </div>
</div>
EOT;
    }
}
