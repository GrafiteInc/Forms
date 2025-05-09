<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\AutoSuggest;

class Timezone extends AutoSuggest
{
    protected static function getOptions()
    {
        return [
            'options' => timezone_identifiers_list(),
        ];
    }
}
