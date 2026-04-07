<?php

namespace Grafite\Forms\Fields;

class Timezone extends AutoSuggestSelect
{
    protected static function getOptions()
    {
        return [
            'options' => timezone_identifiers_list(),
        ];
    }
}
