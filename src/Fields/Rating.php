<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Rating extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/fontawesome-stars.css',
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/fontawesome-stars-o.css',
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/css-stars.css',
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/bars-pill.css',
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/bars-movie.css',
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/bars-square.css',
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/bars-1to10.css',
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/bars-reversed.css',
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/dist/themes/bars-horizontal.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/jquery-bar-rating@1.2.2/jquery.barrating.min.js',
        ];
    }

    public static function onLoadJsData($id, $options)
    {
        return $options['theme'] ?? 'fontawesome-stars-o';
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_rating_field';
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_rating_field = function (element) {
            $(element).barrating({
                theme: element.getAttribute('data-formsjs-onload-data')
            });
        }
JS;
    }
}
