<?php

namespace Grafite\Forms\Fields\Bootstrap;

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
        return [
            "//cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css",
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js'
        ];
    }

    public static function styles($id, $options)
    {
        return <<<EOT
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

var content = "<input type='text' class='bss-input' onkeydown='event.stopPropagation();' onkeypress='Forms_select_addSelectInpKeyPress(this,event)' onclick='event.stopPropagation()' placeholder='$addItemPlaceholder'> <span class='fas fa-plus addnewicon' onclick='Forms_select_addSelectItem(this,event,1);'></span>";

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
$('#${id}').selectpicker({
    style: "{$btn}"
}).parent().css({
    display: "block",
    width: "100%"
});
EOT;
    }
}
