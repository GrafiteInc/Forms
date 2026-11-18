<?php

namespace Grafite\Forms\Fields\Bootstrap;

/**
 * A DropdownSelect with a search box at the top of the menu that filters
 * the options as you type (case and accent insensitive).
 *
 * Typing while the toggle is focused opens the menu and starts the search,
 * Enter picks the first match, and the arrow keys move between the search
 * box and the options.
 *
 * Options:
 * - `search_placeholder`: placeholder for the search box (default "Search...")
 * - `no_results_label`: text shown when nothing matches (default "No results found")
 */
class SearchableDropdownSelect extends DropdownSelect
{
    protected static function fieldOptions()
    {
        return [
            'search_placeholder',
            'no_results_label',
        ];
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'search' => true,
            'placeholder' => $options['search_placeholder'] ?? 'Search...',
            'noResults' => $options['no_results_label'] ?? 'No results found',
        ]);
    }
}
