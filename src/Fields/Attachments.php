<?php

namespace App\Http\Forms\Fields;

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
        ];
    }

    protected static function getFactory()
    {
        return 'image';
    }

    protected static function getTemplate()
    {
        return <<<EOT
<div class="form-group">
    <label for="{id}" class="{labelClass}">{name}</label>
    {field}
    <div class="attachments-list"></div>
    {errors}
</div>
EOT;
    }

    protected static function styles($id, $options)
    {
        return '';
    }

    protected static function js($id, $options)
    {
        return <<<EOT
var Forms_attachments = [];
let Forms_listAttachments = function (attachments) {
    let _list = document.querySelector('.attachments-list');
        _list.innerHTML = "";
    let filesElement = document.createElement('ul');
        filesElement.className = 'list-group list-group-flush';

    for (let i = 0; i < attachments.length; i++) {
        let attachment = attachments[i];

        for (let j = 0; j < attachment.files.length; j++) {
            let file = attachment.files[j];

            var sizes = ['B', 'KB', 'MB', 'GB'];
            fileSize = file.size;
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
                fileSizeBadge.className = 'badge badge-primary float-right';

            let deleteIcon = document.createElement('span');
                deleteIcon.className = 'fas fa-trash';
            let deleteButton = document.createElement('button');
                deleteButton.className = 'btn btn-sm float-right btn-outline-danger mr--20 ml-2';
                deleteButton.appendChild(deleteIcon);
                deleteButton.addEventListener('click', event => {
                    event.preventDefault();
                    document.getElementById(attachment.getAttribute('id')).remove();
                    attachments.splice(i, 1);
                    Forms_listAttachments(attachments);
                });

            let fileElement = document.createElement('li');
                fileElement.classList.add('list-group-item');
                fileElement.appendChild(nameSpan);
                fileElement.appendChild(deleteButton);
                fileElement.appendChild(fileSizeBadge);

            filesElement.appendChild(fileElement);
        }
    }

    document.querySelector('.attachments-list').appendChild(filesElement);
}

let Forms_setAttachmentBindings = function () {
    document.getElementById('{$id}').addEventListener('change', function () {
        let filename = this.value;
        this.setAttribute('id', 'attachment_' + Forms_attachments.length);
        this.setAttribute('style', 'display: none;');
        let _inputContainer = document.querySelector('.custom-file');

        let _inputField = document.createElement('input');
            _inputField.setAttribute('type', 'file');
            _inputField.setAttribute('id', '{$id}');
            _inputField.setAttribute('name', 'attachments[]');
            _inputField.setAttribute('class', 'form-control custom-file-input attachments');

        _inputContainer.appendChild(_inputField);

        if (filename !== '') {
            Forms_attachments.push(this);
        }

        Forms_listAttachments(Forms_attachments);

        Forms_setAttachmentBindings();
    });
}

Forms_setAttachmentBindings();
EOT;
    }
}
