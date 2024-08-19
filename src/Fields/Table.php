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
<div class="form-group mb-3">
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
            window._formsjs_tableActionBinding = function (element) {
                let _id = element.getAttribute('name');

                document.querySelectorAll(`.\${_id}-add-item`).forEach(function (_item) {
                    _item.removeEventListener('click', _formsjs_tableAddItem);
                    _item.addEventListener('click', _formsjs_tableAddItem);
                });

                document.querySelectorAll(`.\${_id}-remove-item`).forEach(function (_item) {
                    _item.removeEventListener('click', _formsjs_tableRemoveRow);
                    _item.addEventListener('click', _formsjs_tableRemoveRow);
                });
            }

            window._formsjs_tableCreateRow = function (element, item, _template, _index, _makeClearRow) {
                let _id = element.getAttribute('name');
                let _nextItem = _template.cloneNode(true);
                    _nextItem.setAttribute('data-item-number', _index);
                    _nextItem.querySelector('.input-group-text').innerHTML = (_index + 1);
                    _nextItem.querySelectorAll(`.\${_id}-item-input`).forEach(function (_input, index) {
                        _input.setAttribute('name', `\${_id}[\${_index}][]`);
                        if (typeof item[index] != 'undefined') {
                            _input.value = item[index];
                        }

                        if (_makeClearRow) {
                            _input.value = '';
                        }
                    });

                    _nextItem.querySelectorAll(`.\${_id}-remove-item`).forEach(function (_input) {
                        _input.setAttribute('data-item-number', _index);
                    });
                    _nextItem.querySelectorAll(`.\${_id}-add-item`).forEach(function (_input) {
                        _input.setAttribute('data-item-number', _index);
                    });

                element.parentNode.appendChild(_nextItem);
            }

            window._formsjs_tableRemoveRow = function (e) {
                e.preventDefault();
                let _row = e.target;
                if (e.target.matches('.fa.fa-minus')) {
                    _row = e.target.parentNode;
                }
                let _number = _row.getAttribute('data-item-number');
                let _element = e.target.parentNode.closest('.form-group').querySelector('input[data-formsjs-onload]');
                let _id = _element.getAttribute('name');

                document.querySelector(`.\${_id}-item-row[data-item-number="\${_number}"]`).remove();
                let _name = _element.getAttribute('id').toLowerCase() + '-item-row';

                document.querySelectorAll('.' + _name).forEach(function (node, index) {
                    node.setAttribute('data-item-number', index);
                    node.querySelector('.input-group-text').innerHTML = (index + 1);
                    node.querySelectorAll('input').forEach(function (input) {
                        input.setAttribute('name', _element.getAttribute('id').toLowerCase() + '['+index+'][]')
                    });
                    node.querySelectorAll('button').forEach(function (button) {
                        button.setAttribute('data-item-number', index);
                    });
                });

                let event = new Event('change', { 'bubbles': true });
                _element.dispatchEvent(event);

                _formsjs_tableActionBinding(_element);
            }

            window._formsjs_tableAddItem = function (e) {
                e.preventDefault();
                let _row = e.target;
                if (e.target.matches('.fa.fa-plus')) {
                    _row = e.target.parentNode;
                }

                let _index = [];
                let _element = _row.parentNode.closest('.form-group').querySelector('input[data-formsjs-onload]');
                let _name = _element.getAttribute('id').toLowerCase() + '-item-row';

                document.querySelectorAll('.' + _name).forEach(function (node, index) {
                    _index.push(index);
                    node.setAttribute('data-item-number', index);
                    node.querySelector('.input-group-text').innerHTML = (index + 1);
                    node.querySelectorAll('input').forEach(function (input) {
                        input.setAttribute('name', _element.getAttribute('id').toLowerCase() + '['+index+'][]')
                    });
                });

                let _max = Math.max.apply(this, _index);

                window._formsjs_tableCreateRow(_element, _row, _row.parentNode, _max + 1, true);

                let event = new Event('change', { 'bubbles': true });
                _element.dispatchEvent(event);

                window._formsjs_tableActionBinding(_element);
            }

            _formsjs_getTableRowTemplate = function (element) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                let _template = '';
                let _id = element.getAttribute('name');

                [0].forEach (function (_row) {
                    _template += `<div class="input-group mb-2 \${_id}-item-row" data-item-number="\${_row}">`;
                    _template += `<div class="input-group-text">\${_row}</div>`;
                    [...Array(_config.columns).keys()].forEach (function (_column) {
                        _template += `<input name="\${_id}[\${_row}][]" type="text" class="form-control \${_id}-item-input">`;
                    });
                    _template += `<button class="btn btn-outline-warning \${_id}-remove-item" type="button" data-item-number="\${_row}"><span class="fa fa-minus"></span></button>`;
                    _template += `<button class="btn btn-outline-primary \${_id}-add-item" type="button" data-item-number="\${_row}"><span class="fa fa-plus"></span></button>`;
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
                            _tableValue.forEach(function (item, _index) {
                                _formsjs_tableCreateRow(element, item, _tableLastRow, _index, false);
                            });
                    } else {
                        _formsjs_tableCreateRow(element, [], _tableLastRow, 0, true);
                    }

                    _formsjs_tableActionBinding(element);
                }
            }
JS;
    }
}
