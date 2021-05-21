<?php

namespace App\Http\Forms\Fields;

use Exception;
use Grafite\Forms\Fields\Field;

class FilePond extends Field
{
    protected static function getType()
    {
        return 'file';
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'image';
    }

    protected static function stylesheets($options)
    {
        return [
            '//unpkg.com/filepond/dist/filepond.css',
        ];
    }

    protected static function scripts($options)
    {
        return [
            '//unpkg.com/filepond/dist/filepond.min.js',
            '//unpkg.com/jquery-filepond/filepond.jquery.js'
        ];
    }

    protected static function getTemplate($options)
    {
        return <<<EOT
<div class="filepond-wrapper mb-4">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="filepond-previews"></div>
    {errors}
</div>
EOT;
    }

    protected static function styles($id, $options)
    {
        return <<<EOT
.filepond-previews {
    border-radius: 4px;
    height: 250px;
    border: 1px solid #CCC;
    background-color: #FFF;
}
.filepond--action-process-item{
    visibility:hidden;
}
EOT;
    }

    protected static function js($id, $options)
    {
        $url = url('/');
        $fileSize = $options['file_size'] ?? '25MB';
        $processUrl = $options['process_url'] ?? null;
        $formSubmitBtn = $options['submit_button'] ?? 'button[type="submit"]';
        $uploadResultField = $options['upload_result_field'] ?? null;

        if (empty($processUrl)) {
            throw new Exception("Must have route for file uploads.", 1);
        }

        if (empty($uploadResultField)) {
            throw new Exception("You need to have a field to inject the upload results into.", 1);
        }

        return <<<EOT
        $(function () {
            $.fn.filepond.setDefaults({
                maxFileSize: '{$fileSize}',
                instantUpload: false,
                allowMultiple: true
            });

            $('.filepond-previews').filepond();
            $('.filepond-previews').filepond('setOptions', {
                server: {
                    url: '{$url}',
                    process: '{$processUrl}',
                }
            });

            let _form = $('.filepond-previews').parent().parent('form');
            let _files = [];

            $(_form).submit(function (e) {
                e.preventDefault();
                $('{$formSubmitBtn}').attr('disabled', 'disabled');

                $('.filepond-previews').filepond('processFiles').then(files => {
                    $('input[name="filepond"]').remove();

                    $('{$uploadResultField}').val(files);

                    _form[0].submit();
                });
            });
        });
EOT;
    }
}
