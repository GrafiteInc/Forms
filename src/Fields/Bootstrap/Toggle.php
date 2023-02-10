<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

class Toggle extends Field
{
    protected static function getType()
    {
        return 'checkbox';
    }

    protected static function getOptions()
    {
        return [
            'label' => false,
        ];
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'boolean';
    }

    public static function stylesheets($options)
    {
        return [
            "//cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css",
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js'
        ];
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_toggleField';
    }

    public static function onLoadJsData($id, $options)
    {
        $themeScript = "'light'";

        if (isset($options['theme']) && is_bool($options['theme']) && $options['theme']) {
            $themeScript = "window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'";
        }

        if (isset($options['theme']) && is_string($options['theme'])) {
            $theme = $options['theme'];
            $themeScript = "'{$theme}'";
        }

        return json_encode([
            'theme' => $themeScript,
            'on' => $options['on'] ?? 'On',
            'off' => $options['off'] ?? 'Off',
            'size' => $options['size'] ?? 'sm',
            'label_class' => $options['label_class'] ?? 'bootstrap-toggle-label',
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
            _formsjs_toggleField = function (element) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                $(element).bootstrapToggle({
                    offstyle: _config.theme,
                    on: _config.on,
                    off: _config.off,
                    size: _config.size
                });

                $(element).parent().siblings('label').addClass(_config.label_class);
            }
JS;
    }
}
