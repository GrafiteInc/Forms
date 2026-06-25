<?php

namespace Grafite\Forms\Fields;

class Signature extends Field
{
    protected static function fieldOptions()
    {
        return [
            'height',
            'clear_label',
            'pen_color',
            'background_color',
            'min_width',
            'max_width',
            'format',
        ];
    }

    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getFactory()
    {
        return 'text(600)';
    }

    public static function scripts($options)
    {
        return [
            '//cdn.jsdelivr.net/npm/signature_pad@5.0.10/dist/signature_pad.umd.min.js',
        ];
    }

    public static function getTemplate($options)
    {
        $height = $options['height'] ?? 220;
        $clearLabel = $options['clear_label'] ?? 'Clear Signature';

        return <<<HTML
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        {field}
        <div class="forms-signature-wrapper">
            <canvas id="Signature_{id}" class="forms-signature-canvas" style="height: {$height}px;"></canvas>
            <button type="button" id="Signature_{id}_Clear" class="btn btn-outline-secondary btn-sm mt-2">{$clearLabel}</button>
        </div>
        {errors}
    </div>
</div>
HTML;
    }

    public static function styles($id, $options)
    {
        return <<<'CSS'
.forms-signature-wrapper {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    background-color: #fff;
    padding: 0.5rem;
}

.forms-signature-canvas {
    width: 100%;
    display: block;
    border: 1px dashed #ced4da;
    border-radius: 0.25rem;
    background-color: #fff;
}

@media (prefers-color-scheme: dark) {
    .forms-signature-wrapper {
        border-color: #333;
        background-color: #111;
    }

    .forms-signature-canvas {
        border-color: #444;
        background-color: #111;
    }
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_signatureField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'penColor' => $options['pen_color'] ?? '#111111',
            'backgroundColor' => $options['background_color'] ?? '#ffffff',
            'minWidth' => $options['min_width'] ?? 0.5,
            'maxWidth' => $options['max_width'] ?? 2.5,
            'format' => $options['format'] ?? 'image/png',
        ]);
    }

    public static function js($id, $options)
    {
        return <<<'JS'
        window._formsjs_signatureField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _id = element.getAttribute('id');
                let _canvas = document.getElementById('Signature_' + _id);
                let _clearButton = document.getElementById('Signature_' + _id + '_Clear');

                if (! _canvas || typeof SignaturePad === 'undefined') {
                    return;
                }

                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data') || '{}');

                let _signaturePad = new SignaturePad(_canvas, {
                    penColor: _config.penColor || '#111111',
                    backgroundColor: _config.backgroundColor || '#ffffff',
                    minWidth: _config.minWidth || 0.5,
                    maxWidth: _config.maxWidth || 2.5,
                });

                let _restoreValue = function () {
                    if (element.value) {
                        _signaturePad.fromDataURL(element.value, {
                            ratio: Math.max(window.devicePixelRatio || 1, 1),
                        });
                    }
                };

                let _resizeCanvas = function () {
                    let _ratio = Math.max(window.devicePixelRatio || 1, 1);
                    let _width = _canvas.offsetWidth;
                    let _height = _canvas.offsetHeight;

                    _canvas.width = _width * _ratio;
                    _canvas.height = _height * _ratio;
                    _canvas.getContext('2d').scale(_ratio, _ratio);

                    _signaturePad.clear();
                    _restoreValue();
                };

                _resizeCanvas();
                window.addEventListener('resize', _resizeCanvas);

                _signaturePad.addEventListener('endStroke', function () {
                    element.value = _signaturePad.isEmpty()
                        ? ''
                        : _signaturePad.toDataURL(_config.format || 'image/png');

                    element.dispatchEvent(new Event('change', { bubbles: true }));
                });

                if (_clearButton) {
                    _clearButton.addEventListener('click', function () {
                        _signaturePad.clear();
                        element.value = '';
                        element.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                }

                if (element.disabled || element.readOnly) {
                    _signaturePad.off();

                    if (_clearButton) {
                        _clearButton.setAttribute('disabled', 'disabled');
                    }
                }
            }
        }
JS;
    }
}