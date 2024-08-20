<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class Attachments extends Field
{
    protected static function getType()
    {
        return 'custom-file';
    }

    protected static function getOptions()
    {
        return [
            'name' => 'attachments[]',
            'class' => 'attachments',
            'deleteButton' => 'btn btn-sm float-right btn-outline-danger me--20 ms-2',
            'inputClass' => 'form-control custom-file-input attachments',
        ];
    }

    protected static function getFactory()
    {
        return 'image';
    }

    public static function getTemplate($options)
    {
        return <<<HTML
<div class="form-group">
    <label for="{id}" class="{labelClass}">{name}</label>
    {field}
    <div class="attachments-list"></div>
    {errors}
</div>
HTML;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_AttachmentsField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'delete_button' => $options['deleteButton'],
            'input_class' => $options['inputClass'],
        ]);
    }

    public static function js($id, $options)
    {
        $listGroup = config('forms.html.list-group', 'list-group list-group-flush');
        $listGroupItem = config('forms.html.list-group-item', 'list-group-item');
        $badge = config('forms.html.badge-tag', 'badge badge-primary float-end');

        return <<<JS
            window._formsjs_attachmentBindings = function (element) {
                element.addEventListener('change', function () {
                    let _id = element.getAttribute('id');
                    let _class = element.getAttribute('class');
                    let filename = this.value;
                    this.setAttribute('id', 'attachment_' + _formsjs_attachments.length);
                    this.setAttribute('style', 'display: none;');
                    let _inputContainer = element.parentNode.parentNode.querySelector('.custom-file');

                    let _inputField = document.createElement('input');
                        _inputField.setAttribute('type', 'file');
                        _inputField.setAttribute('id', _id);
                        _inputField.setAttribute('name', 'attachments[]');
                        _inputField.setAttribute('class', _class);
                        _inputField.setAttribute('data-formsjs-onload-data', element.getAttribute('data-formsjs-onload-data'));
                        _inputField.setAttribute('data-formsjs-onload', element.getAttribute('data-formsjs-onload'));


                    _inputContainer.appendChild(_inputField);

                    if (filename !== '') {
                        _formsjs_attachments.push(this);
                    }

                    _formsjs_attachmentsList(_inputField);
                    _formsjs_attachmentBindings(_inputField);
                });
            }

            window._formsjs_attachmentsList = function (element) {
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                let attachments = _formsjs_attachments;

                let _list = element.parentNode.parentNode.querySelector('.attachments-list');
                    _list.innerHTML = "";
                let filesElement = document.createElement('ul');
                    filesElement.className = '{$listGroup}';

                for (let i = 0; i < attachments.length; i++) {
                    let attachment = attachments[i];

                    for (let j = 0; j < attachment.files.length; j++) {
                        let file = attachment.files[j];

                        var sizes = ['B', 'KB', 'MB', 'GB'];
                        var fileSize = file.size;
                        var sizeIndex = 0;

                        while (fileSize > 1024) {
                            sizeIndex++;
                            fileSize = fileSize/1024;
                        }

                        fileSize = Math.round(fileSize*10) / 10 + sizes[sizeIndex];
                        let _name = file.name.substring(0, 30);

                        if (file.name.length > 30) {
                            _name = _name + '...';
                        }

                        let nameSpan = document.createElement('span');
                            nameSpan.innerText = _name;

                        let fileSizeBadge = document.createElement('span');
                            fileSizeBadge.innerText = fileSize;
                            fileSizeBadge.className = '{$badge}';

                        let deleteIcon = document.createElement('span');
                            deleteIcon.className = 'fas fa-trash';
                        let deleteButton = document.createElement('button');
                            deleteButton.className = _config.delete_button;
                            deleteButton.appendChild(deleteIcon);
                            deleteButton.addEventListener('click', event => {
                                event.preventDefault();
                                document.getElementById(attachment.getAttribute('id')).remove();
                                attachments.splice(i, 1);
                                _formsjs_attachmentsList(element);
                            });

                        let fileElement = document.createElement('li');
                            fileElement.classList.add('{$listGroupItem}');
                            fileElement.appendChild(nameSpan);
                            fileElement.appendChild(deleteButton);
                            fileElement.appendChild(fileSizeBadge);

                        filesElement.appendChild(fileElement);
                    }
                }

                element.parentNode.parentNode.querySelector('.attachments-list').appendChild(filesElement);
            }

            window._formsjs_AttachmentsField = function (element) {
                if (! element.getAttribute('data-formsjs-rendered')) {
                    let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                    _formsjs_attachments = [];

                    _formsjs_attachmentsList(element);
                    _formsjs_attachmentBindings(element);
                }
            }
JS;
    }
}
