<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Illuminate\Support\Str;
use Grafite\Forms\Fields\Field;

class Select2 extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getAttributes()
    {
        return [
            // 'class' => 'selectpicker',
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
        // $version = (Str::of(config('forms.bootstrap-version'))->startsWith('5')) ? '1.14.0-beta2' : 'latest';

        return [
            '//cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css',

            '//cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css',
            // '//cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',
        ];
    }

    public static function styles($id, $options)
    {
        return <<<EOT
        @media (prefers-color-scheme: dark) {
            .select2-selection {
                background-color: #1F1F1F !important;
                border: 2px solid #444 !important;
            }
            .select2-selection__rendered {
                color: #FFF !important;
            }
            .select2-dropdown {
                background-color: #333 !important;
                border: 2px solid #444 !important;
            }
            .select2-search__field {
                background-color: #1F1F1F !important;
                border: 2px solid #444 !important;
                color: #FFF !important;
            }
            .select2-results__option--highlighted {
                background-color: #222 !important;
                color: #FFF !important;
            }
        }
EOT;
    }

    public static function js($id, $options)
    {
        $btn = $options['btn'] ?? 'btn-outline-primary';
        $withAddItem = $options['add-item'] ?? false;
        $addItemPlaceholder = $options['add-item-placeholder'] ?? 'Add Item';

        $addItem = <<<EOT
window.Forms_select_addSelectItem = function (t, ev) {
    ev.stopPropagation();

    var bs = $(t).closest('.bootstrap-select')
    var txt = bs.find('.bss-input').val().replace(/[|]/g,"");
    var txt = $(t).prev().val().replace(/[|]/g,"");

    if ($.trim(txt) == '') return;

    var p = bs.find('select');
    var o = $('option', p).eq(-2);
    o.before( $("<option>", {"text": txt, "value": txt}) );

    $('#${id}').selectpicker('refresh').parent().css({
        display: "block",
        width: "100%"
    });

    $('#${id}').val(txt);

    $('#${id}').selectpicker('refresh').parent().css({
        display: "block",
        width: "100%"
    });
}

window.Forms_select_addSelectInpKeyPress = function (t, ev) {
   ev.stopPropagation();

   // do not allow pipe character
   if (ev.which == 124) ev.preventDefault();

   // enter character adds the option
   if (ev.which == 13) {
      ev.preventDefault();
      window.Forms_select_addSelectItem($(t).next(),ev);
   }
}

var formsWithInputWhiteList = $.fn.selectpicker.Constructor.DEFAULTS.whiteList;
formsWithInputWhiteList.input = ['type', 'placeholder', 'onkeypress', 'onkeydown', 'onclick'];
formsWithInputWhiteList.span = ['onclick'];

var content = "<input type='text' class='bss-input' onkeydown='event.stopPropagation();' onkeypress='Forms_select_addSelectInpKeyPress(this,event)' onclick='event.stopPropagation()' placeholder='${addItemPlaceholder}'> <span class='fas fa-plus addnewicon' onclick='Forms_select_addSelectItem(this,event,1);'></span>";

var divider = $('<option/>')
    .addClass('divider')
    .attr('data-divider', true);

var addoption = $('<option/>', {class: 'addItem'})
    .attr('data-content', content);

$('#${id}')
    .append(divider)
    .append(addoption)
.selectpicker({
    style: "{$btn}"
}).parent().css({
    display: "block",
    width: "100%"
});
EOT;

        if ($withAddItem) {
            return $addItem;
        }

        return <<<EOT
$(document).ready(function() {
    $('#${id}').select2({
        theme: "bootstrap-5",
        // minimumInputLength: 3,
        // maximumSelectionLength: 2,
        minimumResultsForSearch: Infinity,
        width: $(this).data("width") ? $(this).data("width") : $(this).hasClass("w-100")  ? "100%" : "style",
    });
});
EOT;
    }
}
