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

                items.push({ item: item, option: option });
                li.appendChild(item);
                menu.appendChild(li);
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

                    Array.from(child.children).forEach(function (option) {
                        addItem(option, child.disabled);
                    });
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

            wrapper.appendChild(toggle);
            wrapper.appendChild(menu);
            element.after(wrapper);
            element.classList.add('d-none');
        }
JS;
    }
}
