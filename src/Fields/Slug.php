<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Slug extends Field
{
    protected static function getType()
    {
        return 'text';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    protected static function js($id, $options)
    {
        return <<<EOT
document.getElementById('${id}')
.addEventListener("keyup", event => {
    event.preventDefault();
    let str = document.getElementById('${id}').value;
    str = str.replace(/\W+(?!$)/g, '-').toLowerCase();
    document.getElementById('${id}').value = str;
});
EOT;
    }
}
