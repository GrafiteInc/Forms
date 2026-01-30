<?php

namespace Grafite\Forms\Parsers;

interface FieldParser
{
    /**
     * Parse the content and hand back the parser.
     *
     * @param  mixed  $content
     * @return self
     */
    public function parse($content);

    /**
     * Handle the parsing of the content
     *
     * @param  mixed  $content
     * @return self|void
     */
    public function handler($content);

    /**
     * Render the content as a string
     *
     * @return mixed
     */
    public function render();
}
