<?php

namespace Grafite\Forms\Fields;

class Trix extends Field
{
    protected static function fieldOptions()
    {
        return [
            'quill_theme',
            'toolbars',
        ];
    }

    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getAttributes()
    {
        return [
            'style' => 'height: 200px;',
        ];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    public static function stylesheets($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/trix@2.1.10/dist/trix.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/trix@2.1.10/dist/trix.umd.min.js',
        ];
    }

    public static function getTemplate($options)
    {
        return <<<'HTML'
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        {field}
        <trix-editor input="{id}"></trix-editor>
        {errors}
    </div>
</div>
HTML;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_trixField';
    }

    public static function onLoadJsData($id, $options)
    {
        $route = null;

        if (isset($options['upload_route'])) {
            $route = route($options['upload_route']);
        }

        $placeholder = $options['placeholder'] ?? '';

        return json_encode([
            'route' => $route,
            'placeholder' => $placeholder,
        ]);
    }

    public static function js($id, $options)
    {
        return <<<'JS'
            window._formsjs_trixField = function (element) {
                element.addEventListener('grafite-form-change', function (event) {
                    let _method = element.form.getAttribute('data-formsjs-onchange');
                        _method = _method.replace('(event)', '');
                    window[_method](event);
                });

                if (! element.getAttribute('data-formsjs-rendered')) {
                    let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));
                    document.addEventListener("trix-attachment-add", function(event) {
                        if (event.attachment.file) {
                            window._formsjs_trix_file_upload_processor(event.attachment)
                        }
                    });

                    window._formsjs_trix_file_upload_processor = function (attachment) {
                        console.log(attachment)
                        let setProgress = function (progress) {
                            console.log(progress)
                            attachment.setUploadProgress(progress)
                        }

                        let setAttributes = function (attributes) {
                            attachment.setAttributes(attributes)
                        }

                        window._formsjs_trix_file_upload(attachment, setProgress, setAttributes);
                    }

                    window._formsjs_trix_file_upload = function (attachment, setProgressCallback, setAttributesCallback) {
                        const _FileFormData = new FormData();
                            _FileFormData.append('image', attachment.file);

                        window.axios
                            .post(_config.route, _FileFormData, {
                                headers: {
                                  "Content-Type": "multipart/form-data",
                                },
                                setProgressCallback,
                            })
                            .then(response => {
                                setAttributesCallback({
                                    url: response.data.file.url
                                });
                            })
                            .catch(error => {
                                console.log('Image upload failed');
                                console.log(error);
                            });
                    };
                }
            };
JS;
    }
}
