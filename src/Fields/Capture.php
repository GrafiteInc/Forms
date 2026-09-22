<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Services\FieldConfigProcessor;

class Capture extends Field
{
    protected static function fieldOptions()
    {
        return [
            'width',
            'height',
            'format',
            'quality',
            'facing_mode',
            'as_file',
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

    /**
     * Make the field config, switching the input to a file when in file mode
     *
     * The type is resolved by the static getType() which has no access to the
     * options, so the swap happens on the config once it has been built.
     *
     * @param  string  $name
     * @param  array  $options
     */
    public static function make($name, $options = []): FieldConfigProcessor
    {
        $config = parent::make($name, $options);

        if (static::isFileMode($options)) {
            $config->type = 'file';
            $config->factory = 'image';
            $config->attributes['accept'] = 'image/*';
            $config->attributes['capture'] = static::getFacingMode($options);
        }

        return $config;
    }

    /**
     * Whether the capture is submitted as an uploaded file
     *
     * @param  array  $options
     * @return bool
     */
    protected static function isFileMode($options)
    {
        return (bool) ($options['as_file'] ?? false);
    }

    /**
     * Resolve the camera to request
     *
     * @param  array  $options
     * @return string
     */
    protected static function getFacingMode($options)
    {
        return ($options['facing_mode'] ?? 'user') === 'environment' ? 'environment' : 'user';
    }

    /**
     * Resolve the image format of the captured frame
     *
     * @param  array  $options
     * @return string
     */
    protected static function getFormat($options)
    {
        return ($options['format'] ?? 'image/jpeg') === 'image/png' ? 'image/png' : 'image/jpeg';
    }

    public static function getTemplate($options)
    {
        $facingMode = static::getFacingMode($options);
        $width = (int) ($options['width'] ?? 0);
        $height = (int) ($options['height'] ?? 0);

        $ratio = ($width > 0 && $height > 0)
            ? ' style="aspect-ratio: '.$width.' / '.$height.';"'
            : '';

        // In file mode the field itself is the fallback picker, so it stays in
        // the markup and is only hidden once a camera stream is running. In
        // data url mode the picker is unnamed and never submitted, it only
        // feeds the hidden input.
        $fallback = static::isFileMode($options)
            ? ''
            : '<input type="file" id="Capture_{id}_Fallback" class="forms-capture-fallback" accept="image/*" capture="'.$facingMode.'" hidden>';

        return <<<HTML
<div class="{rowClass}">
    <label for="{id}" class="{labelClass}">{name}</label>
    <div class="{fieldClass}">
        {field}
        {$fallback}
        <div class="forms-capture-wrapper" id="Capture_{id}" hidden>
            <video id="Capture_{id}_Video" class="forms-capture-media" playsinline autoplay muted{$ratio}></video>
            <img id="Capture_{id}_Preview" class="forms-capture-media" alt="" hidden{$ratio}>
            <canvas id="Capture_{id}_Canvas" hidden></canvas>
            <div class="forms-capture-actions">
                <button type="button" id="Capture_{id}_Button" class="btn btn-outline-secondary btn-sm">Capture</button>
                <button type="button" id="Capture_{id}_Retake" class="btn btn-outline-secondary btn-sm" hidden>Retake</button>
            </div>
        </div>
        <div class="forms-capture-error" id="Capture_{id}_Error" role="alert" hidden></div>
        {errors}
    </div>
</div>
HTML;
    }

    public static function styles($id, $options)
    {
        return <<<'CSS'
.forms-capture-wrapper {
    --forms-capture-border-color: var(--bs-border-color, #ced4da);
    --forms-capture-bg: var(--bs-body-bg, #ffffff);
    --forms-capture-media-bg: #000000;
    --forms-capture-error-color: var(--bs-danger, #dc3545);
    padding: 0.5rem;
    background-color: var(--forms-capture-bg);
    border: 1px solid var(--forms-capture-border-color);
    border-radius: var(--bs-border-radius, 0.375rem);
}

.forms-capture-media {
    width: 100%;
    display: block;
    aspect-ratio: 4 / 3;
    object-fit: contain;
    background-color: var(--forms-capture-media-bg);
    border-radius: var(--bs-border-radius, 0.375rem);
}

.forms-capture-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.forms-capture-error {
    margin-top: 0.5rem;
    color: var(--bs-danger, #dc3545);
    font-size: 0.875rem;
}

@media (prefers-color-scheme: dark) {
    .forms-capture-wrapper {
        --forms-capture-border-color: var(--bs-border-color, #495057);
        --forms-capture-bg: var(--bs-body-bg, #212529);
    }
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_captureField';
    }

    public static function onLoadJsData($id, $options)
    {
        $format = static::getFormat($options);

        return json_encode([
            'width' => isset($options['width']) ? (int) $options['width'] : null,
            'height' => isset($options['height']) ? (int) $options['height'] : null,
            'format' => $format,
            'quality' => (float) ($options['quality'] ?? 0.92),
            'facingMode' => static::getFacingMode($options),
            'asFile' => static::isFileMode($options),
            'fileName' => $format === 'image/png' ? 'capture.png' : 'capture.jpg',
        ]);
    }

    public static function js($id, $options)
    {
        return <<<'JS'
        window._formsjs_captureField = function (element) {
            if (element.getAttribute('data-formsjs-rendered')) {
                return;
            }

            let _id = element.getAttribute('id');
            let _wrapper = document.getElementById('Capture_' + _id);

            if (! _wrapper) {
                return;
            }

            let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data') || '{}');
            let _video = document.getElementById('Capture_' + _id + '_Video');
            let _canvas = document.getElementById('Capture_' + _id + '_Canvas');
            let _preview = document.getElementById('Capture_' + _id + '_Preview');
            let _captureButton = document.getElementById('Capture_' + _id + '_Button');
            let _retakeButton = document.getElementById('Capture_' + _id + '_Retake');
            let _error = document.getElementById('Capture_' + _id + '_Error');
            let _fallback = document.getElementById('Capture_' + _id + '_Fallback');

            if (! _video || ! _canvas) {
                return;
            }

            let _isFileMode = _config.asFile === true;
            let _fileInput = _isFileMode ? element : _fallback;
            let _stream = null;

            let _notify = function () {
                element.dispatchEvent(new Event('change', { bubbles: true }));
            };

            let _stopStream = function () {
                if (_stream) {
                    _stream.getTracks().forEach(function (_track) {
                        _track.stop();
                    });

                    _stream = null;
                }
            };

            let _messageFor = function (_err) {
                let _name = _err && _err.name ? _err.name : '';

                if (_name === 'NotAllowedError' || _name === 'SecurityError') {
                    return 'Camera access was blocked. Choose an image file instead.';
                }

                if (_name === 'NotFoundError' || _name === 'OverconstrainedError') {
                    return 'No camera was found. Choose an image file instead.';
                }

                return 'The camera is unavailable. Choose an image file instead.';
            };

            let _showFallback = function (_message) {
                _stopStream();
                _wrapper.hidden = true;

                if (_error) {
                    _error.textContent = _message;
                    _error.hidden = false;
                }

                if (_fileInput) {
                    _fileInput.hidden = false;
                }
            };

            let _showPreview = function (_source) {
                if (_preview) {
                    _preview.src = _source;
                    _preview.hidden = false;
                }

                _video.hidden = true;

                if (_captureButton) {
                    _captureButton.hidden = true;
                }

                if (_retakeButton) {
                    _retakeButton.hidden = false;
                }
            };

            let _showCamera = function () {
                if (_preview) {
                    _preview.hidden = true;
                    _preview.removeAttribute('src');
                }

                _video.hidden = false;

                if (_captureButton) {
                    _captureButton.hidden = false;
                }

                if (_retakeButton) {
                    _retakeButton.hidden = true;
                }
            };

            let _startStream = function () {
                if (! navigator.mediaDevices || ! navigator.mediaDevices.getUserMedia) {
                    _showFallback('This browser cannot use the camera. Choose an image file instead.');

                    return;
                }

                navigator.mediaDevices.getUserMedia({
                    video: { facingMode: _config.facingMode || 'user' },
                    audio: false,
                }).then(function (_mediaStream) {
                    _stream = _mediaStream;
                    _video.srcObject = _mediaStream;

                    if (_error) {
                        _error.hidden = true;
                    }

                    // Reveal the wrapper before playing, a video in a hidden
                    // container gets throttled in some browsers.
                    _wrapper.hidden = false;

                    if (_isFileMode) {
                        element.hidden = true;
                    }

                    _showCamera();

                    // videoWidth is 0 until metadata lands, so hold the button
                    // back rather than let an early click silently do nothing.
                    if (_captureButton && ! element.disabled && ! element.readOnly) {
                        _captureButton.disabled = true;

                        _video.addEventListener('loadedmetadata', function () {
                            _captureButton.disabled = false;
                        }, { once: true });
                    }

                    let _playing = _video.play();

                    if (_playing && _playing.catch) {
                        _playing.catch(function () {});
                    }
                }).catch(function (_err) {
                    _showFallback(_messageFor(_err));
                });
            };

            let _capture = function () {
                let _width = _config.width || _video.videoWidth;
                let _height = _config.height || _video.videoHeight;

                if (! _width || ! _height) {
                    return;
                }

                let _format = _config.format || 'image/jpeg';
                let _quality = typeof _config.quality === 'number' ? _config.quality : 0.92;

                _canvas.width = _width;
                _canvas.height = _height;
                _canvas.getContext('2d').drawImage(_video, 0, 0, _width, _height);

                let _dataUrl = _canvas.toDataURL(_format, _quality);

                if (! _isFileMode) {
                    element.value = _dataUrl;
                    _showPreview(_dataUrl);
                    _stopStream();
                    _notify();

                    return;
                }

                _canvas.toBlob(function (_blob) {
                    if (! _blob) {
                        return;
                    }

                    let _transfer = new DataTransfer();

                    _transfer.items.add(new File([_blob], _config.fileName || 'capture.jpg', {
                        type: _format,
                    }));

                    element.files = _transfer.files;

                    _showPreview(_dataUrl);
                    _stopStream();
                    _notify();
                }, _format, _quality);
            };

            let _retake = function () {
                if (_isFileMode) {
                    element.files = new DataTransfer().files;
                } else {
                    element.value = '';
                }

                _notify();
                _startStream();
            };

            if (_captureButton) {
                _captureButton.addEventListener('click', _capture);
            }

            if (_retakeButton) {
                _retakeButton.addEventListener('click', _retake);
            }

            if (_fallback) {
                _fallback.addEventListener('change', function () {
                    let _file = _fallback.files && _fallback.files[0];

                    if (! _file) {
                        return;
                    }

                    let _reader = new FileReader();

                    _reader.addEventListener('load', function () {
                        element.value = _reader.result;
                        _wrapper.hidden = false;
                        _video.hidden = true;

                        if (_preview) {
                            _preview.src = _reader.result;
                            _preview.hidden = false;
                        }

                        if (_captureButton) {
                            _captureButton.hidden = true;
                        }

                        if (_retakeButton) {
                            _retakeButton.hidden = true;
                        }

                        _notify();
                    });

                    _reader.readAsDataURL(_file);
                });
            }

            window.addEventListener('pagehide', _stopStream);

            if (element.disabled || element.readOnly) {
                [_captureButton, _retakeButton, _fallback].forEach(function (_node) {
                    if (_node) {
                        _node.setAttribute('disabled', 'disabled');
                    }
                });
            }

            if (! _isFileMode && String(element.value || '').indexOf('data:image') === 0) {
                _wrapper.hidden = false;
                _showPreview(element.value);

                return;
            }

            if (! element.disabled && ! element.readOnly) {
                _startStream();
            }
        }
JS;
    }
}
