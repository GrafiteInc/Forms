<?php

namespace App\Http\Forms\Fields;

use Grafite\FormMaker\Fields\Field;

class Attachments extends Field
{
    protected static function getType()
    {
        return 'custom-file';
    }

    protected static function getOptions()
    {
        return [
            'multiple' => true
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
let _listAttachments = function (attachments, element) {
    element.html("");
    let filesElement = $('<ul/>').addClass('list-group list-group-flush');
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

            fileElement = $('<li/>').addClass('list-group-item')
                .append($('<span>').text(file.name))
                .append($('<span class="badge badge-primary float-right">').text(fileSize));

            filesElement.append(fileElement);
        }
    }

    $(element).append(filesElement);
}

$('#{$id}').bind('change', function () {
    let attachments = [];
    let filename = $(this).val();

    if (filename !== '') {
        attachments.push(this);
    }

    _listAttachments(attachments, $('.attachments-list'));
});
EOT;
    }
}
