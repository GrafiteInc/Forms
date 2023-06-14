<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class GrapesJs extends Field
{
    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getAttributes()
    {
        return [
            'style' => 'height: 800px;',
        ];
    }

    protected static function getFactory()
    {
        return 'text(300)';
    }

    public static function stylesheets($options)
    {
        return [
            '//unpkg.com/grapesjs/dist/css/grapes.min.css',
        ];
    }

    public static function scripts($options)
    {
        return [
            '//unpkg.com/grapesjs',
            '//unpkg.com/grapesjs-blocks-basic@1.0.1',
            '//unpkg.com/grapesjs-preset-webpage@1.0.2',
        ];
    }

    public static function getTemplate($options)
    {
        return <<<HTML
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        <div id="{id}_Grapes">{value}</div>
        {field}
        {errors}
    </div>
</div>
HTML;
    }

    public static function styles($id, $options)
    {
        return <<<CSS
            .gjs-select {
                max-height: 25px;
            }
        CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_grapesjsField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([]);
    }

    public static function js($id, $options)
    {
        return <<<JS
                _formsjs_grapesjsField = function (element) {
                    if (! element.getAttribute('data-formsjs-rendered')) {
                        let _id = element.getAttribute('id');
                        let _elementId = _id+'_Grapes';
                        let _instance = '_formsjs_'+ _id + '_GrapesJs';
                        let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                        window[_instance] = grapesjs.init({
                            container: '#'+_elementId,
                            fromElement: true,
                            assetManager: {
                                embedAsBase64: true
                            },
                            height: '800px',
                            width: 'auto',

                            storageManager: {
                                type: 'local',
                                options: {
                                    local: { key: `gjsProject-\${_id}` }
                                }
                            },
                            plugins: [
                                'gjs-blocks-basic',
                                'grapesjs-preset-webpage',
                            ],
                            pluginsOpts: {
                                'gjs-blocks-basic': { flexGrid: true },
                                'grapesjs-preset-webpage': {
                                    modalImportTitle: 'Import Template',
                                    modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
                                    modalImportContent: function(editor) {
                                        return editor.getHtml() + '<style>'+editor.getCss()+'</style>'
                                    },
                                },
                            }
                        });

                        window[_instance].on('storage:store', function (e) {
                            console.log('Stored ', e);
                            element.value = window[_instance].getHtml()
                        });

                        element.form.addEventListener('keydown', function (event) {
                            if (event.keyCode == 13) {
                                event.preventDefault();
                                return false;
                            }
                        });

                        element.form.addEventListener('submit', function (event) {
                            localStorage.removeItem(`gjsProject-\${_id}`);
                        });
                    }
                }
            JS;
    }
}
