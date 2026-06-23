<?php

namespace Grafite\Forms\Services;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

class FormAssets
{
    public $stylesheets = [];

    public $scripts = [];

    public $styles = [];

    public $js = [];

    public $fields = [];

    /**
     * Cached contents of the core JavaScript file. The file never
     * changes at runtime, so we only read it from disk once.
     *
     * @var string|null
     */
    protected static $coreJavaScript = null;

    /**
     * Cache of minified output keyed by a hash of the raw source.
     * Minification is CPU-heavy and the input is identical across
     * renders, so the same source is only minified once per request.
     *
     * @var array<string, string>
     */
    protected static $minifyCache = [];

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
     * @param  array  $stylesheets
     * @return self
     */
    public function addStylesheets($stylesheets)
    {
        foreach ($stylesheets as $sheet) {
            $this->stylesheets[] = '<link href="'.$sheet.'" rel="stylesheet">';
        }

        return $this;
    }

    /**
     * Add field scripts to a form
     *
     * @param  array  $scripts
     * @return self
     */
    public function addScripts($scripts)
    {
        foreach ($scripts as $script) {
            $this->scripts[] = '<script src="'.$script.'"></script>';
        }

        return $this;
    }

    /**
     * Add field Styles code to a form
     *
     * @param  string  $styles
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
     * @param  string  $js
     * @return self
     */
    public function addJs($js)
    {
        if (! is_null($js)) {
            $this->js[] = $js;
        }

        return $this;
    }

    /**
     * Minify a source string, memoizing the result by content hash so
     * identical source is only run through the minifier once per request.
     *
     * @param  string  $type
     * @param  string  $source
     * @return string
     */
    protected static function cachedMinify($type, $source, callable $minifier)
    {
        $key = $type.':'.md5($source);

        if (! isset(static::$minifyCache[$key])) {
            static::$minifyCache[$key] = $minifier($source);
        }

        return static::$minifyCache[$key];
    }

    protected function compileStyles($type, $nonce)
    {
        $nonce = $nonce ? ' nonce="'.$nonce.'"' : '';
        $output = '';

        if (in_array($type, ['all', 'styles'])) {
            $output .= collect($this->stylesheets)->unique()->implode("\n");
            $styles = collect($this->styles)->unique()->implode("\n");

            if (app()->environment('production')) {
                $styles = static::cachedMinify('css', $styles, function ($source) {
                    return (new CSS)->add($source)->minify();
                });
            }

            $output .= "<style {$nonce}>\n{$styles}\n</style>\n";
        }

        return $output;
    }

    protected function compileScripts($type, $nonce = false)
    {
        $nonce = $nonce ? ' nonce="'.$nonce.'"' : '';
        $output = '';

        if (in_array($type, ['all', 'scripts'])) {
            $output .= collect($this->scripts)->unique()->implode("\n");

            if (is_null(static::$coreJavaScript)) {
                static::$coreJavaScript = file_get_contents(__DIR__.'/../JavaScript/core.js');
            }

            $js = collect($this->js)->push(static::$coreJavaScript)->unique()->implode("\n;");

            if (app()->environment('production')) {
                $js = static::cachedMinify('js', $js, function ($source) {
                    return (new JS)->add($source)->minify();
                });
            }

            $function = "window.FormsJS = () => { {$js} };";

            $output .= "<!-- Form Scripts --><script type=\"module\" {$nonce}>\n{$function}\n window.FormsJS();\n</script>\n";
        }

        return $output;
    }
}
