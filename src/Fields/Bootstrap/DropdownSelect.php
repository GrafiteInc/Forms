<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

/**
 * A single-value select rendered as a Bootstrap 5 dropdown, so it looks and
 * behaves the same across browsers and devices.
 *
 * The native <select> stays in the DOM (hidden) so form submission and any
 * `change` listeners keep working. The dropdown is inserted directly after
 * the select so `.is-invalid ~ .invalid-feedback` validation styling still
 * applies. Selects with `multiple` are left as native selects.
 *
 * When the onload data contains `search: true` (see SearchableDropdownSelect)
 * a filter box is added to the top of the menu.
 */
class DropdownSelect extends Field
{
    protected static function getType()
    {
        return 'select';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'form-select',
        ];
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function styles($id, $options)
    {
        return <<<'CSS'
.dropdown-select {
    position: relative;
    width: 100%;
}

.dropdown-select .dropdown-select-toggle {
    display: flex;
    align-items: center;
    width: 100%;
}

.dropdown-select .dropdown-select-label {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    min-width: 0;
}

.dropdown-select .dropdown-select-menu {
    width: 100%;
    min-width: 200px;
    max-height: 60vh;
    overflow-y: auto;
}

.dropdown-select .dropdown-select-menu .dropdown-item {
    white-space: normal;
    overflow-wrap: anywhere;
}

.dropdown-select .dropdown-select-search {
    position: sticky;
    top: calc(-1 * var(--bs-dropdown-padding-y, 0.5rem));
    z-index: 1;
    margin-top: calc(-1 * var(--bs-dropdown-padding-y, 0.5rem));
    margin-bottom: 0.25rem;
    padding: var(--bs-dropdown-padding-y, 0.5rem) var(--bs-dropdown-item-padding-x, 1rem);
    background-color: var(--bs-dropdown-bg, #fff);
}

.dropdown-select .dropdown-select-empty .dropdown-item-text {
    color: var(--bs-secondary-color, #6c757d);
}

.is-invalid ~ .dropdown-select .dropdown-select-toggle {
    border-color: var(--bs-form-invalid-border-color, #dc3545);
}

.is-invalid ~ .dropdown-select .dropdown-select-toggle:focus {
    border-color: var(--bs-form-invalid-border-color, #dc3545);
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-danger-rgb, 220, 53, 69), 0.25);
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_dropdownSelectField';
    }

    public static function js($id, $options)
    {
        return <<<'JS'
        window._formsjs_dropdownSelectField = function (element) {
            if (element.getAttribute('data-formsjs-rendered') || element.multiple) {
                return;
            }

            let labelFor = function (option) {
                return (option && option.textContent.trim()) || ' ';
            };

            let normalize = function (value) {
                return value.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();
            };

            let config = {};

            try {
                config = JSON.parse(element.getAttribute('data-formsjs-onload-data')) || {};
            } catch (error) {
                config = {};
            }

            let wrapper = document.createElement('div');
            wrapper.className = 'dropdown dropdown-select';

            let toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'form-select text-start dropdown-select-toggle';
            toggle.setAttribute('data-bs-toggle', 'dropdown');
            toggle.setAttribute('aria-expanded', 'false');

            let toggleLabel = document.createElement('span');
            toggleLabel.className = 'dropdown-select-label';
            toggle.appendChild(toggleLabel);

            let fieldLabel = (element.labels && element.labels.length) ? element.labels[0] : null;
            let ariaLabel = element.getAttribute('title')
                || element.getAttribute('aria-label')
                || (fieldLabel ? fieldLabel.textContent.trim() : '');

            if (ariaLabel) {
                toggle.setAttribute('aria-label', ariaLabel);
            }

            if (fieldLabel) {
                fieldLabel.addEventListener('click', function (event) {
                    if (wrapper.contains(event.target)) {
                        return;
                    }

                    event.preventDefault();
                    toggle.focus();
                });
            }

            let menu = document.createElement('ul');
            menu.className = 'dropdown-menu dropdown-select-menu';

            let items = [];
            let groups = [];

            let addItem = function (option, groupDisabled) {
                let li = document.createElement('li');
                let item = document.createElement('button');
                item.type = 'button';
                item.className = 'dropdown-item';
                item.setAttribute('data-value', option.value);
                item.textContent = labelFor(option);
                item.disabled = option.disabled || groupDisabled;

                item.addEventListener('click', function () {
                    if (option.selected) {
                        return;
                    }

                    option.selected = true;
                    element.dispatchEvent(new Event('change', { bubbles: true }));
                });

                let entry = { li: li, item: item, option: option, text: normalize(labelFor(option)) };

                items.push(entry);
                li.appendChild(item);
                menu.appendChild(li);

                return entry;
            };

            Array.from(element.children).forEach(function (child) {
                if (child.tagName === 'OPTGROUP') {
                    if (child.children.length === 0) {
                        return;
                    }

                    let header = document.createElement('li');
                    let h6 = document.createElement('h6');
                    h6.className = 'dropdown-header';
                    h6.textContent = child.label;
                    header.appendChild(h6);
                    menu.appendChild(header);

                    let group = { header: header, entries: [] };

                    Array.from(child.children).forEach(function (option) {
                        group.entries.push(addItem(option, child.disabled));
                    });

                    groups.push(group);
                } else if (child.tagName === 'OPTION') {
                    addItem(child, false);
                }
            });

            let sync = function () {
                let selected = element.options[element.selectedIndex] || null;

                toggleLabel.textContent = labelFor(selected);
                toggle.disabled = element.disabled;

                items.forEach(function (entry) {
                    let active = entry.option === selected;

                    entry.item.classList.toggle('active', active);

                    if (active) {
                        entry.item.setAttribute('aria-current', 'true');
                    } else {
                        entry.item.removeAttribute('aria-current');
                    }
                });
            };

            element.addEventListener('change', sync);

            if (element.form) {
                element.form.addEventListener('reset', function () {
                    setTimeout(sync);
                });
            }

            sync();

            if (config.search) {
                wrapper.classList.add('dropdown-select-searchable');

                let searchItem = document.createElement('li');
                searchItem.className = 'dropdown-select-search';

                let search = document.createElement('input');
                search.type = 'search';
                search.className = 'form-control form-control-sm';
                search.autocomplete = 'off';
                search.placeholder = config.placeholder || 'Search...';
                search.setAttribute('aria-label', config.placeholder || 'Search...');
                searchItem.appendChild(search);
                menu.prepend(searchItem);

                let empty = document.createElement('li');
                empty.className = 'dropdown-select-empty';
                empty.hidden = true;

                let emptyText = document.createElement('span');
                emptyText.className = 'dropdown-item-text';
                emptyText.textContent = config.noResults || 'No results found';
                empty.appendChild(emptyText);
                menu.appendChild(empty);

                let filter = function () {
                    let query = normalize(search.value);
                    let matches = 0;

                    items.forEach(function (entry) {
                        let match = query === '' || entry.text.indexOf(query) !== -1;

                        entry.li.hidden = ! match;

                        if (match) {
                            matches++;
                        }
                    });

                    groups.forEach(function (group) {
                        group.header.hidden = group.entries.every(function (entry) {
                            return entry.li.hidden;
                        });
                    });

                    empty.hidden = matches > 0;
                };

                let visibleItems = function () {
                    return items.filter(function (entry) {
                        return ! entry.li.hidden && ! entry.item.disabled;
                    }).map(function (entry) {
                        return entry.item;
                    });
                };

                let isTypedCharacter = function (event) {
                    return event.key.length === 1
                        && event.key !== ' '
                        && ! event.ctrlKey
                        && ! event.metaKey
                        && ! event.altKey;
                };

                search.addEventListener('input', filter);

                search.addEventListener('keydown', function (event) {
                    if (event.key === 'ArrowDown') {
                        let first = visibleItems()[0];

                        event.preventDefault();

                        if (first) {
                            first.focus();
                        }
                    } else if (event.key === 'Enter') {
                        let first = visibleItems()[0];

                        // Never let Enter in the search box submit the form.
                        event.preventDefault();

                        if (first) {
                            first.click();
                        }
                    }
                });

                // Bootstrap handles dropdown arrow keys in a document capture listener and
                // stops propagation, so menu keys are routed through one window capture
                // listener that runs first.
                if (! window._formsjs_dropdownSelectKeydown) {
                    window._formsjs_dropdownSelectKeydown = true;

                    window.addEventListener('keydown', function (event) {
                        let target = event.target.closest ? event.target.closest('.dropdown-select-menu') : null;

                        if (target && target._formsjsKeydown) {
                            target._formsjsKeydown(event);
                        }
                    }, true);
                }

                menu._formsjsKeydown = function (event) {
                    if (event.target === search) {
                        return;
                    }

                    if (event.key === 'ArrowUp' && event.target === visibleItems()[0]) {
                        event.preventDefault();
                        event.stopPropagation();
                        search.focus();
                    } else if (isTypedCharacter(event) || event.key === 'Backspace') {
                        // Move focus without preventing the default so the key lands in the search box.
                        search.focus();
                    }
                };

                toggle.addEventListener('keydown', function (event) {
                    if (! isTypedCharacter(event) || toggle.classList.contains('show')) {
                        return;
                    }

                    event.preventDefault();
                    search.value = event.key;
                    filter();
                    toggle.click();
                });

                toggle.addEventListener('shown.bs.dropdown', function () {
                    let active = menu.querySelector('.dropdown-item.active');

                    if (search.value === '' && active) {
                        menu.scrollTop = Math.max(0, active.offsetTop - searchItem.offsetHeight);
                    }

                    search.focus();
                    search.setSelectionRange(search.value.length, search.value.length);
                });

                toggle.addEventListener('hidden.bs.dropdown', function () {
                    if (search.value !== '') {
                        search.value = '';
                        filter();
                    }

                    // Keyboard selection leaves focus inside the now-hidden menu.
                    if (menu.contains(document.activeElement)) {
                        toggle.focus();
                    }
                });
            }

            wrapper.appendChild(toggle);
            wrapper.appendChild(menu);
            element.after(wrapper);
            element.classList.add('d-none');
        }
JS;
    }
}
