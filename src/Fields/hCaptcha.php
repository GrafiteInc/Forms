<?php

namespace Grafite\Forms\Fields;

class hCaptcha extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function scripts($options)
    {
        return [
            'https://js.hcaptcha.com/1/api.js',
        ];
    }

    public static function getTemplate($options)
    {
        $key = config('services.hcaptcha.sitekey');

        return <<<HTML
            <div class="h-captcha" data-sitekey="{$key}"></div>
        HTML;
    }
}
