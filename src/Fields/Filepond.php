<?php

namespace Grafite\Forms\Fields;

use Exception;
use Grafite\Forms\Fields\Field;

class Filepond extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getAttributes()
    {
        return [];
    }

    protected static function getFactory()
    {
        return 'image';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/filepond@4.31.1/dist/filepond.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/filepond@4.31.1/dist/filepond.min.js',
            '//cdn.jsdelivr.net/npm/jquery-filepond@1.0.0/filepond.jquery.min.js',
        ];
    }

    public static function getTemplate($options)
    {
        return <<<HTML
<div class="filepond-wrapper mb-4">
    <label for="{id}" class="{labelClass}">{name}</label>
    {field}
    <div class="filepond-previews" {attributes}></div>
    {errors}
</div>
HTML;
    }

    public static function styles($id, $options)
    {
        return <<<CSS
.filepond-previews {
    border-radius: 4px;
    height: 250px;
    border: 1px solid #CCC;
    background-color: #FFF;
}
.filepond--action-process-item{
    visibility:hidden;
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_FilePondField';
    }

    public static function onLoadJsData($id, $options)
    {
        if (empty($options['process_url'])) {
            throw new Exception("Must have route for file uploads.", 1);
        }

        return json_encode([
            'file_size' => $options['file_size'] ?? '25MB',
            'process_url' => $options['process_url'] ?? null,
            'submit_button' => $options['submit_button'] ?? 'button[type="submit"]',
        ]);
    }

    public static function js($id, $options)
    {
        $url = url('/');

        return <<<JS
            _formsjs_FilePondField = function (element) {
                if (! element.getAttribute('data-formsjs-rendered')) {
                    let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                    $.fn.filepond.setDefaults({
                        maxFileSize: _config.size,
                        instantUpload: false,
                        allowMultiple: true
                    });

                    $('.filepond-previews').filepond();
                    $('.filepond-previews').filepond('setOptions', {
                        server: {
                            url: '{$url}',
                            process: {
                                url: '/'+_config.process_url,
                                method: 'POST',
                                headers: {
                                    "X-CSRF-TOKEN": document.head.querySelector('meta[name="csrf-token"]').content
                                }
                            },
                        }
                    });

                    let _form = $('.filepond-previews').parent().parent('form');
                    let _files = [];

                    $(_form).submit(function (e) {
                        e.preventDefault();
                        $(_config.submit_button).attr('disabled', 'disabled');

                        $('.filepond-previews').filepond('processFiles').then(files => {
                            $('input[name="filepond"]').remove();

                            $(element).val(files);

                            _form[0].submit();
                        });
                    });
                }
            }
JS;
    }
}
