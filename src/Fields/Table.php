<?php

namespace Grafite\Forms\Fields;

use Illuminate\Support\Str;
use Grafite\Forms\Fields\Field;

class Table extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getOptions()
    {
        return [
            'columns' => 2,
            'inputClass' => 'form-control',
        ];
    }

    protected static function getFactory()
    {
        return 'text';
    }

    public static function getTemplate($options, $value)
    {
        return <<<EOT
<div class="form-group">
    <label for="{id}" class="{labelClass}">{name}</label>
    {field}
    {errors}
</div>
EOT;
    }

    public static function styles($id, $options)
    {
        return '';
    }

    public static function js($id, $options)
    {
            $rows = [0];
            $fields = '';
            $id = Str::of($id)->lower();

            foreach ($rows as $row) {
                $fields .= "<div class=\"input-group mb-3 ${id}-item-row\" data-item-number=\"${row}\">";
                foreach (range(1, $options['columns'] ?? 2) as $column) {
                    $fields .= "<input name=\"{$id}[${row}][]\" type=\"text\" class=\"form-control ${id}-item-input\" placeholder=\"\">";
                }
                $fields .= "<button class=\"btn btn-outline-secondary ${id}-remove-item\" type=\"button\" data-item-number=\"${row}\" onclick=\"window.Forms_removeItemAction_${id}(event)\"><span class=\"fa fa-minus\"></span></button>";
                $fields .= "<button class=\"btn btn-outline-secondary ${id}-add-item\" type=\"button\" data-item-number=\"${row}\" onclick=\"window.Forms_addItemAction_${id}(event)\"><span class=\"fa fa-plus\"></span></button>";
                $fields .= '</div>';
            }

        return <<<EOT
window.Forms_lastTableRowHtml_${id} = '$fields';
window.Forms_lastTableRow_${id} = new DOMParser().parseFromString(window.Forms_lastTableRowHtml_${id}, "text/html").body.firstElementChild;

window.Forms_createItemRow_${id} = function (item) {
    let _rowCount = document.querySelectorAll('.${id}-item-row').length;
    let _rowCountLast = _rowCount++;
    let _nextItem = window.Forms_lastTableRow_${id}.cloneNode(true);
        _nextItem.setAttribute('data-item-number', _rowCountLast);
        let _rowName = '{$id}[' + _rowCountLast + '][]';
        _nextItem.querySelectorAll('.${id}-item-input').forEach(function (_input, index) {
            _input.setAttribute('name', _rowName);
            if (typeof item[index] != 'undefined') {
                _input.value = item[index];
            }
        });
        _nextItem.querySelectorAll('.${id}-remove-item').forEach(function (_input) {
            _input.setAttribute('data-item-number', _rowCountLast);
        });
        _nextItem.querySelectorAll('.${id}-add-item').forEach(function (_input) {
            _input.setAttribute('data-item-number', _rowCountLast);
        });
    window.Forms_rootTableForm_${id}.appendChild(_nextItem);
}

window.Forms_addItemAction_${id} = function (e) {
    e.preventDefault();
    let _row = e.target;
    if (e.target.matches('.fa.fa-plus')) {
        _row = e.target.parentNode;
    }
    window.Forms_createItemRow_${id}(_row);
}

window.Forms_removeItemAction_${id} = function (e) {
    e.preventDefault();
    let _row = e.target;
    if (e.target.matches('.fa.fa-minus')) {
        _row = e.target.parentNode;
    }
    let _number = _row.getAttribute('data-item-number');
    document.querySelector('.${id}-item-row[data-item-number="'+_number+'"]').remove();
}

window.Forms_rootTableInput_${id} = document.querySelector('input[name="${id}"][type="hidden"]');
window.Forms_rootTableForm_${id} = window.Forms_rootTableInput_${id}.parentNode;

if (window.Forms_rootTableInput_${id}.value) {
    window.Forms_tableInputValue_${id} = JSON.parse(window.Forms_rootTableInput_${id}.value);
    window.Forms_tableInputValue_${id}.forEach(function (item) {
        window.Forms_createItemRow_${id}(item);
    });
} else {
    window.Forms_createItemRow_${id}([]);
}

window.Forms_rootTableInput_${id}.remove();
EOT;
    }
}
