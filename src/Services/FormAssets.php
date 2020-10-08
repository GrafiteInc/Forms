<?php

namespace Grafite\Forms\Services;

use MatthiasMullie\Minify\JS;
use MatthiasMullie\Minify\CSS;
use Grafite\Forms\Traits\HasLivewire;

class FormAssets
{
    use HasLivewire;

    public $stylesheets = [];

    public $scripts = [];

    public $styles = [];

    public $js = [];

    public $withLivewire = false;

    public $livewireOnKeydown = false;

    /**
     * Render the form assets
     *
     * @return string
     */
    public function render()
    {
        $output = '';

        $output .= collect($this->stylesheets)->unique()->implode("\n");
        $output .= collect($this->scripts)->unique()->implode("\n");

        $styles = collect($this->styles)->unique()->implode("\n");
        if (app()->environment('production')) {
            $minifierCSS = new CSS();
            $styles = $minifierCSS->add($styles)->minify();
        }
        $output .= "<style>\n{$styles}\n</style>\n";

        $js = collect($this->js)->unique()->implode("\n");

        if (app()->environment('production')) {
            $minifierJS = new JS();
            $js = $minifierJS->add($js)->minify();
        }

        $livewire = '';

        if ($this->withLivewire) {
            $livewire = "Livewire.hook('message.processed', (el, component) => {
                {$js}
            })";
        }

        $output .= "<script>\n{$js}\n{$livewire}\n</script>\n";

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
        if (!is_null($styles)) {
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
        if (!is_null($js)) {
            $this->js[] = $js;
        }

        return $this;
    }
}
