<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

class SimpleDatalist extends Field
{
    protected static function getType()
    {
        return 'input';
    }

    protected static function getAttributes()
    {
        return [
            'class' => 'form-control',
        ];
    }

    protected static function getFactory()
    {
        return 'text(50)';
    }

    public static function stylesheets($options)
    {
        return [];
    }

    public static function scripts($options)
    {
        return [];
    }

    public static function styles($id, $options)
    {
        return <<<CSS
.bs-select {
    position: relative;
    width: 100%;
    height: 36px;
}

.bs-select select {
    display: none;
}

.simple-datalist-selected {
    background-color: var(--bs-input-bg);
    border-color: var(--bs-border-color);
    border-width: var(--bs-border-width);
    border-radius: var(--bs-border-radius);
    border-style: var(--bs-border-style);
}

.simple-datalist-selected:after {
    position: absolute;
    content: "";
    top: 16px;
    right: 12px;
    width: 0;
    height: 0;
    border: 6px solid transparent;
    border-color: var(--bs-body-color) transparent transparent transparent;
}

.simple-datalist-selected.simple-datalist-arrow-active:after {
  border-color: transparent transparent var(--bs-body-color) transparent;
  top: 10px;
}

.simple-datalist-selected {
    color: var(--bs-body-color);
    padding: 6px 12px;
    cursor: pointer;
    border-color: var(--bs-border-color);
    border-width: var(--bs-border-width);
    border-radius: var(--bs-border-radius);
    border-style: var(--bs-border-style);
}

.simple-datalist-items div {
    color: var(--bs-body-color);
    padding: 8px 12px;
    cursor: pointer;
}

.simple-datalist-items {
    position: absolute;
    background-color: var(--bs-body-bg);
    top: 100%;
    left: 0;
    right: 0;
    z-index: 99;
    margin-top: 6px;
    max-height: 300px;
    overflow-y: auto;
    box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0.1);
    border-top-color: var(--bs-border-color);
    border-top-width: var(--bs-border-width);
    border-top-left-radius: var(--bs-border-radius);
    border-top-right-radius: var(--bs-border-radius);
    border-top-style: var(--bs-border-style);
    border-left-color: var(--bs-border-color);
    border-left-width: var(--bs-border-width);
    border-left-style: var(--bs-border-style);
    border-right-color: var(--bs-border-color);
    border-right-width: var(--bs-border-width);
    border-right-style: var(--bs-border-style);
    border-bottom-width: var(--bs-border-width);
    border-bottom-color: var(--bs-border-color);
    border-bottom-style: var(--bs-border-style);
    border-bottom-left-radius: var(--bs-border-radius);
    border-bottom-right-radius: var(--bs-border-radius);
}

.simple-datalist-hide {
    display: none;
}

.simple-datalist-items div:first-child {
    border-top-left-radius: var(--bs-border-radius);
    border-top-right-radius: var(--bs-border-radius);
}

.simple-datalist-items div:last-child {
    border-bottom-left-radius: var(--bs-border-radius);
    border-bottom-right-radius: var(--bs-border-radius);
}

.simple-datalist-items div:hover, .same-as-selected {
    background-color: var(--bs-primary);
    color: var(--bs-white) !important;
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_bootstrapSimpleDatalistField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            'options' => $options['datalist'] ?? [],
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        window._formsjs_bootstrapSimpleDatalistField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _id = element.getAttribute('id');
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                var wrapper = document.createElement('div');
                    wrapper.setAttribute('class', 'bs-select');
                    element.parentNode.insertBefore(wrapper, element);
                    wrapper.appendChild(element);

                /* For each element, create a new DIV that will contain the option list: */
                var dropdownContainer = document.createElement("DIV");
                    dropdownContainer.setAttribute("class", "simple-datalist-items simple-datalist-hide");

                for (j = 0; j < _config.options.length; j++) {
                    var c = document.createElement("DIV");
                        c.innerHTML = _config.options[j];

                    if (_config.options[j]) {
                        c.setAttribute("data-value", _config.options[j]);
                    } else {
                        c.setAttribute("data-value", null);
                    }

                    dropdownContainer.appendChild(c);
                }

                wrapper.appendChild(dropdownContainer);

                [...dropdownContainer.children].forEach(elem => {
                    elem.addEventListener('click', function (event) {
                        element.value = this.innerHTML;
                        var event = new Event('change', {
                            bubbles: true
                        });
                        element.dispatchEvent(event);
                        _formsjs_closeAllBsSimpleDatalist(element);
                    });
                });

                element.addEventListener("focus", function(e) {
                    _formsjs_closeAllBsSimpleDatalist(this);
                    this.nextSibling.classList.toggle("simple-datalist-hide");
                    this.classList.toggle("simple-datalist-arrow-active");
                });

                element.addEventListener("keyup", function(e) {
                    [...dropdownContainer.children].forEach(elem => {
                        if (! elem.innerHTML.includes(element.value)) {
                            elem.classList.add("d-none");
                        } else {
                            elem.classList.remove("d-none");
                        }
                    });

                    if (element.value === '') {
                        [...dropdownContainer.children].forEach(elem => {
                            elem.classList.remove("d-none");
                        });
                    }

                    if (_config.options.length === element.nextSibling.querySelectorAll('.d-none').length) {
                        element.nextSibling.classList.add("simple-datalist-hide");
                    } else {
                        element.nextSibling.classList.remove("simple-datalist-hide");
                    }
                });

                // NEED TO THINK ON THIS
                element.addEventListener("blur", function(e) {
                    setTimeout(() => {
                        _formsjs_closeAllBsSimpleDatalist(this);
                    }, 450);
                });

                function _formsjs_closeAllBsSimpleDatalist(elmnt) {
                    // refactor this
                    var x, y, i, xl, yl, arrNo = [];
                    x = document.getElementsByClassName("simple-datalist-items");
                    y = document.getElementsByClassName("simple-datalist-selected");
                    xl = x.length;
                    yl = y.length;
                    for (i = 0; i < yl; i++) {
                        if (elmnt == y[i]) {
                            arrNo.push(i)
                        } else {
                            y[i].classList.remove("simple-datalist-arrow-active");
                        }
                    }
                    for (i = 0; i < xl; i++) {
                        if (arrNo.indexOf(i)) {
                            x[i].classList.add("simple-datalist-hide");
                        }
                    }
                };

                // document.addEventListener("click", _formsjs_closeAllBsSimpleDatalist);
                document.addEventListener("keyup", function (e) {
                    if (e.key === 'Escape') {
                        _formsjs_closeAllBsSimpleDatalist();
                    }
                });
            }
        }
JS;
    }
}
