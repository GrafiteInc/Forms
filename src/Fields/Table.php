<?php

namespace Grafite\Forms\Fields;

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

    public static function getTemplate($options)
    {
        return <<<HTML
<div class="form-group">
    <label for="{id}" class="{labelClass}">{name}</label>
    {field}
    {errors}
</div>
HTML;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_tableField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'columns' => $options['columns'],
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
            _formsjs_tableCreateRow = function (element, item, _template) {
                let _id = element.getAttribute('name');
                let _rowCount = document.querySelectorAll('.'+_id+'-item-row').length;
                let _rowCountLast = _rowCount++;
                let _nextItem = _template.cloneNode(true);
                    _nextItem.setAttribute('data-item-number', _rowCountLast);
                        let _rowName = `\${_id}[\${_rowCountLast}][]`;

                    _nextItem.querySelectorAll(`.\${_id}-item-input`).forEach(function (_input, index) {
                        _input.setAttribute('name', _rowName);
                        if (typeof item[index] != 'undefined') {
                            _input.value = item[index];
                        }
                    });
                    _nextItem.querySelectorAll(`.\${_id}-remove-item`).forEach(function (_input) {
                        _input.setAttribute('data-item-number', _rowCountLast);
                    });
                    _nextItem.querySelectorAll(`.\${_id}-add-item`).forEach(function (_input) {
                        _input.setAttribute('data-item-number', _rowCountLast);
                    });

                    element.parentNode.appendChild(_nextItem);
            }

            _formsjs_tableRemoveRow = function (e) {
                e.preventDefault();
                let _row = e.target;
                if (e.target.matches('.fa.fa-minus')) {
                    _row = e.target.parentNode;
                }
                let _number = _row.getAttribute('data-item-number');
                let _element = e.target.parentNode.closest('.form-group').querySelector('input[data-formsjs-onload]');
                let _id = _element.getAttribute('name');

                document.querySelector(`.\${_id}-item-row[data-item-number="\${_number}"]`).remove();
            }

            _formsjs_tableAddItem = function (e) {
                e.preventDefault();
                let _row = e.target;
                if (e.target.matches('.fa.fa-plus')) {
                    _row = e.target.parentNode;
                }

                let _element = _row.parentNode.closest('.form-group').querySelector('input[data-formsjs-onload]');
                // TODO can I clean up the input elements inside this "template"
                _formsjs_tableCreateRow(_element, _row, _row.parentNode);
            }

            _formsjs_getTableRowTemplate = function (element) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                let _template = '';
                let _id = element.getAttribute('name');

                [0].forEach (function (_row) {
                    _template += `<div class="input-group mb-3 \${_id}-item-row" data-item-number="\${_row}">`;
                    [...Array(_config.columns).keys()].forEach (function (_column) {
                        _template += `<input name="\${_id}[\${_row}][]" type="text" class="form-control \${_id}-item-input">`;
                    });
                    _template += `<button class="btn btn-outline-secondary \${_id}-remove-item" type="button" data-item-number="\${_row}" onclick="_formsjs_tableRemoveRow(event)"><span class="fa fa-minus"></span></button>`;
                    _template += `<button class="btn btn-outline-secondary \${_id}-add-item" type="button" data-item-number="\${_row}" onclick="_formsjs_tableAddItem(event)"><span class="fa fa-plus"></span></button>`;
                    _template += `</div>`;
                });

                return _template;
            }

            _formsjs_tableField = function (element) {
                if (! element.getAttribute('data-formsjs-rendered')) {
                    _tableRowTemplate = _formsjs_getTableRowTemplate(element);
                    _tableLastRow = new DOMParser().parseFromString(_tableRowTemplate, "text/html").body.firstElementChild;

                    if (element.value) {
                        let _tableValue = JSON.parse(element.value);
                            _tableValue.forEach(function (item) {
                                _formsjs_tableCreateRow(element, item, _tableLastRow);
                            });
                    } else {
                        _formsjs_tableCreateRow(element, [], _tableLastRow);
                    }
                }
            }
JS;
    }
}
