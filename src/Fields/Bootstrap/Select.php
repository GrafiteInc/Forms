<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Illuminate\Support\Str;
use Grafite\Forms\Fields\Field;

class Select extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'selectpicker',
            'multiple' => true,
            'data-size' => 8,
        ];
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function stylesheets($options)
    {
        $version = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? '1.14.0-beta3' : 'latest';

        return [
            "//cdn.jsdelivr.net/npm/bootstrap-select@{$version}/dist/css/bootstrap-select.min.css",
        ];
    }

    public static function scripts($options)
    {
        $version = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? '1.14.0-beta3' : 'latest';

        return [
            "//cdn.jsdelivr.net/npm/bootstrap-select@{$version}/dist/js/bootstrap-select.min.js",
        ];
    }

    public static function styles($id, $options)
    {
        $color = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? 'var(--bs-gray-400)' : 'rgba(0, 0, 0, .1)';
        $borderLight = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? "1px solid $color !important" : "1px solid $color !important";
        $borderDark = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? "2px solid #444 !important" : "1px solid #444 !important";

        $themes['light'] = <<<CSS
    .bootstrap-select button.dropdown-toggle, button.dropdown-toggle:active {
        color: #111 !important;
        border: $borderLight;
        background-color: #FFF !important;
    }
CSS;

        $themes['dark'] = <<<CSS
    .bootstrap-select button.dropdown-toggle, button.dropdown-toggle:active {
        color: #FFF !important;
        border: $borderDark;
        background-color: #1F1F1F !important;
    }
    .bootstrap-select .dropdown-menu li a.dropdown-item:hover {
        color: #FFF !important;
    }
    .bootstrap-select .dropdown-menu .no-results {
        background-color: transparent;
    }
    .no-results {
        background: transparent !important;
    }
    .bss-input {
        background-color: #111;
        border-radius: 4px;
        border: 2px solid #333;
        color: #FFF;
    }
CSS;

        $lightTheme = $themes['light'];
        $darkTheme = $themes['dark'];

        $autoTheme = <<<CSS
        @media (prefers-color-scheme: light) {
            {$lightTheme}
        }
        @media (prefers-color-scheme: dark) {
            {$darkTheme}
        }
CSS;

        $themeStyle = (isset($options['theme'])) ? $themes[$options['theme']] : $autoTheme;

        return <<<CSS
.bss-input {
   border:0;
   padding: 6px;
   outline: none;
   color: #000;
   width: 99%;
   margin-right: 20px;
}

.additem .check-mark {
   opacity: 0;
   z-index: -1000;
}

.addnewicon {
   position: relative;
   padding: 4px;
   margin: -8px;
   padding-right: 50px;
   margin-right: -50px;
   color: #aaa;
}

.addnewicon:hover {
   color: #222;
}

.bootstrap-select .dropdown-menu {
    z-index: 4000;
}

.bootstrap-select .dropdown-menu .inner {
    overflow-x: hidden;
}

.bs-searchbox {
    margin-bottom: 12px;
}

{$themeStyle}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_bootstrapSelectField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'btn' => $options['btn'] ?? 'btn-outline-primary',
            'with_add_item' => $options['add-item'] ?? false,
            'add_item_placeholder' => $options['add-item-placeholder'] ?? 'Add Item',
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_bootstrapSelect_addInpKeyPress = function (t, ev, id) {
            ev.stopPropagation();

            // do not allow pipe character
            if (ev.which == 124) ev.preventDefault();

            // enter character adds the option
            if (ev.which == 13) {
                ev.preventDefault();
                _formsjs_bootstrapSelect_addSelectItem($(t).next(), ev, id);
            }
        }

        _formsjs_bootstrapSelect_addSelectItem = function (t, ev, id) {
            ev.stopPropagation();

            let bs = $(t).closest('.bootstrap-select')
            let txt = bs.find('.bss-input').val().replace(/[|]/g,"");
                txt = $(t).prev().val().replace(/[|]/g,"");

            if ($.trim(txt) == '') return;

            let p = bs.find('select');
            let o = $('option', p).eq(-2);
                o.before( $("<option>", {"text": txt, "value": txt}) );

            $(`#\${id}`).selectpicker('destroy')
                .selectpicker()
                .selectpicker('val', txt)
                .parent().css({
                    display: "block",
                    width: "100%"
                });
        }

        _formsjs_bootstrapSelectField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _id = element.getAttribute('id');
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                let formsWithInputWhiteList = $.fn.selectpicker.Constructor.DEFAULTS.whiteList;
                    formsWithInputWhiteList.input = ['type', 'placeholder', 'onkeypress', 'onkeydown', 'onclick'];
                    formsWithInputWhiteList.span = ['onclick'];

                if (_config.with_add_item) {
                    let content = `<input type="text" class="bss-input" onkeydown="event.stopPropagation();" onkeypress="_formsjs_bootstrapSelect_addInpKeyPress(this, event, '\${_id}')" onclick="event.stopPropagation()" placeholder="\${_config.add_item_placeholder}"> <span class="fas fa-plus addnewicon" onclick="_formsjs_bootstrapSelect_addSelectItem(this, event, '\${_id}');"></span>`;

                    let divider = $('<option/>')
                        .addClass('divider')
                        .attr('data-divider', true);

                    let addoption = $('<option/>', {class: 'addItem'})
                        .attr('data-content', content);

                    $(element)
                        .append(divider)
                        .append(addoption)
                    .selectpicker({
                        style: _config.btn
                    }).parent().css({
                        display: "block",
                        width: "100%"
                    });
                }

                if (! _config.with_add_item) {
                    $(element).selectpicker({
                        style: _config.btn
                    }).parent().css({
                        display: "block",
                        width: "100%"
                    });
                }
            }
        }
JS;
    }
}
