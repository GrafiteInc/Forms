<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

/**
 * A select rendered as a Bootstrap 5 dropdown, with a colored status dot
 * opposite each option's label.
 *
 * The native <select> stays in the DOM (hidden) so form submission and any
 * `change` listeners keep working. Pass `indicators` as a map of
 * option value => ['color' => <bootstrap contextual color>, 'status' => <label>].
 */
class StatusSelect extends Field
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

    protected static function fieldOptions()
    {
        return [
            'indicators',
        ];
    }

    public static function styles($id, $options)
    {
        return <<<'CSS'
.status-select {
    position: relative;
    width: 100%;
}

.status-select .status-select-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    gap: 0.5rem;
}

.status-select .status-select-label {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    min-width: 0;
}

.status-select .status-select-menu {
    width: 100%;
    min-width: 200px;
    max-height: 60vh;
    overflow-y: auto;
}

.status-select .status-select-menu .dropdown-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.status-select-dot {
    display: inline-block;
    flex-shrink: 0;
    width: 0.625rem;
    height: 0.625rem;
    border-radius: 50%;
    box-shadow: 0 0 0 2px var(--bs-body-bg);
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_statusSelectField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode((object) ($options['indicators'] ?? []));
    }

    public static function js($id, $options)
    {
        return <<<'JS'
        window._formsjs_statusSelectField = function (element) {
            if (element.getAttribute('data-formsjs-rendered')) {
                return;
            }

            let indicators = {};

            try {
                indicators = JSON.parse(element.getAttribute('data-formsjs-onload-data')) || {};
            } catch (e) {}

            let makeDot = function (value) {
                let indicator = indicators[value];

                if (! indicator) {
                    return null;
                }

                let dot = document.createElement('span');
                dot.className = 'status-select-dot bg-' + (indicator.color || 'secondary');
                dot.setAttribute('title', indicator.status || '');
                dot.setAttribute('aria-hidden', 'true');

                return dot;
            };

            let makeContent = function (target, option) {
                target.replaceChildren();

                let label = document.createElement('span');
                label.className = 'status-select-label';
                label.textContent = option ? option.textContent : '';
                target.appendChild(label);

                if (option) {
                    let dot = makeDot(option.value);

                    if (dot) {
                        target.appendChild(dot);

                        let sr = document.createElement('span');
                        sr.className = 'visually-hidden';
                        sr.textContent = indicators[option.value].status || '';
                        target.appendChild(sr);
                    }
                }
            };

            let wrapper = document.createElement('div');
            wrapper.className = 'dropdown status-select';
            element.parentNode.insertBefore(wrapper, element);
            wrapper.appendChild(element);
            element.classList.add('d-none');

            let toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'form-select text-start status-select-toggle';
            toggle.setAttribute('data-bs-toggle', 'dropdown');
            toggle.setAttribute('aria-expanded', 'false');

            let ariaLabel = element.getAttribute('title') || element.getAttribute('aria-label');

            if (ariaLabel) {
                toggle.setAttribute('aria-label', ariaLabel);
            }

            let menu = document.createElement('ul');
            menu.className = 'dropdown-menu status-select-menu';

            let items = [];

            let addItem = function (option) {
                let li = document.createElement('li');
                let item = document.createElement('button');
                item.type = 'button';
                item.className = 'dropdown-item';
                item.setAttribute('data-value', option.value);
                makeContent(item, option);

                if (option.selected) {
                    item.classList.add('active');
                    item.setAttribute('aria-current', 'true');
                }

                item.addEventListener('click', function () {
                    if (element.value === option.value) {
                        return;
                    }

                    element.value = option.value;

                    items.forEach(function (other) {
                        other.classList.remove('active');
                        other.removeAttribute('aria-current');
                    });

                    item.classList.add('active');
                    item.setAttribute('aria-current', 'true');
                    makeContent(toggle, option);

                    element.dispatchEvent(new Event('change', { bubbles: true }));
                });

                items.push(item);
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

                    Array.from(child.children).forEach(addItem);
                } else if (child.tagName === 'OPTION') {
                    addItem(child);
                }
            });

            makeContent(toggle, element.options[element.selectedIndex] || null);

            wrapper.appendChild(toggle);
            wrapper.appendChild(menu);
        }
JS;
    }
}
