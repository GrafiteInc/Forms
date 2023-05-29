<?php

namespace Grafite\Forms\Services;

use MatthiasMullie\Minify\JS;
use MatthiasMullie\Minify\CSS;

class FormAssets
{
    public $stylesheets = [];
    public $scripts = [];
    public $styles = [];
    public $js = [];
    public $fields = [];

    public function __construct()
    {
        // Nothing here
    }

    /**
     * Render the form assets
     *
     * @return string
     */
    public function render($type = 'all', $nonce = false)
    {
        $output = '';

        $output .= $this->compileStyles($type, $nonce);
        $output .= $this->compileScripts($type, $nonce);

        return $output;
    }

    /**
     * Add field stylesheets to a form
     *
     * @param array $stylesheets
     * @return self
     */
    public function addStylesheets($stylesheets)
    {
        foreach ($stylesheets as $sheet) {
            $this->stylesheets[] = '<link href="' . $sheet . '" rel="stylesheet">';
        }

        return $this;
    }

    /**
     * Add field scripts to a form
     *
     * @param array $scripts
     * @return self
     */
    public function addScripts($scripts)
    {
        foreach ($scripts as $script) {
            $this->scripts[] = '<script src="' . $script . '"></script>';
        }

        return $this;
    }

    /**
     * Add field Styles code to a form
     *
     * @param string $styles
     * @return self
     */
    public function addStyles($styles)
    {
        if (! is_null($styles)) {
            $this->styles[] = $styles;
        }

        return $this;
    }

    /**
     * Add field JS code to a form
     *
     * @param string $js
     * @return self
     */
    public function addJs($js)
    {
        if (! is_null($js)) {
            $this->js[] = $js;
        }

        return $this;
    }

    protected function compileStyles($type, $nonce)
    {
        $nonce = $nonce ? ' nonce="' . $nonce . '"' : '';
        $output = '';

        if (in_array($type, ['all', 'styles'])) {
            $output .= collect($this->stylesheets)->unique()->implode("\n");
            $styles = collect($this->styles)->unique()->implode("\n");

            if (app()->environment('production')) {
                $minifierCSS = new CSS();
                $styles = $minifierCSS->add($styles)->minify();
            }

            $output .= "<style {$nonce}>\n{$styles}\n</style>\n";
        }

        return $output;
    }

    protected function compileScripts($type, $nonce = false)
    {
        $nonce = $nonce ? ' nonce="' . $nonce . '"' : '';
        $output = '';

        if (in_array($type, ['all', 'scripts'])) {
            $output .= collect($this->scripts)->unique()->implode("\n");
            $js = collect($this->js)->push("document.querySelectorAll('[data-formsjs-onload]').forEach(function (element) { let _method = element.getAttribute('data-formsjs-onload');
            window[_method](element); element.setAttribute('data-formsjs-rendered', true); });document.querySelectorAll('[data-formsjs-onchange]').forEach(function (element) {
    let _method = element.getAttribute('data-formsjs-onchange');
    _method = _method.replace('(event)', '');
    element.addEventListener('change', function (event) {
        window[_method](event);
    }); });

    document.querySelectorAll('[data-formsjs-onclick]').forEach(function (element) {
    let _method = element.getAttribute('data-formsjs-onclick');
    _method = _method.replace('(event)', '');
    element.addEventListener('click', function (event) {
        event.preventDefault();
        _method = _method.replace('return ', '');
        _method = _method.replace('window.', '');

        if (_method.includes('Forms_validate_submission')) {
            window.Forms_validate_submission(event.target.form, '<i class=\"fas fa-circle-notch fa-spin mr-2\"></i> Save',event.target);
        } else if (_method.includes('FormsJS_disableOnSubmit')) {
            window.FormsJS_disableOnSubmit(event);
        } else if (_method.includes('.')) {
            let _path = _method.split('.');
            if (_path.length == 2) {
                window[_path[0]][_path[1]](event);
            }

            if (_path.length == 3) {
                window[_path[0]][_path[1]][_path[2]](event);
            }

            if (_path.length == 4) {
                throw new Error('Method nesting is too deep. Max of 3!');
            }
        } else if (typeof window[_method] === 'function') {
            window[_method](event);
        }

    }); });")->unique()->implode("\n");

            if (app()->environment('production')) {
                $minifierJS = new JS();
                $js = $minifierJS->add($js)->minify();
            }

            $function = "window.FormsJS = () => { {$js} }";

            $output .= "<!-- Form Scripts --><script {$nonce}>\n{$function}\nwindow.FormsJS();\n</script>\n";
        }

        return $output;
    }
}
