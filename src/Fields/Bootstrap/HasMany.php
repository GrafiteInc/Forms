<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

class HasMany extends Field
{
    protected static function getType()
    {
        return 'relationship';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker',
            'multiple' => true,
        ];
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    protected static function stylesheets($options)
    {
        return [
            "//cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css",
        ];
    }

    protected static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js'
        ];
    }

    protected static function js($id, $options)
    {
        $btn = $options['btn'] ?? 'btn-primary';

        return <<<EOT
$('.selectpicker').selectpicker({
    style: "{$btn}",
}).parent().css({
    display: "block",
    width: "100%"
});
EOT;
    }
}
