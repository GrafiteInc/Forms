<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;

class SimpleSelect extends Field
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

.select-selected {
    background-color: var(--bs-input-bg);
    border-color: var(--bs-border-color);
    border-width: var(--bs-border-width);
    border-radius: var(--bs-border-radius);
    border-style: var(--bs-border-style);
}

.select-selected:after {
    position: absolute;
    content: "";
    top: 16px;
    right: 12px;
    width: 0;
    height: 0;
    border: 6px solid transparent;
    border-color: var(--bs-body-color) transparent transparent transparent;
}

.select-selected.select-arrow-active:after {
  border-color: transparent transparent var(--bs-body-color) transparent;
  top: 10px;
}

.select-selected {
    color: var(--bs-body-color);
    padding: 6px 12px;
    cursor: pointer;
    border-color: var(--bs-border-color);
    border-width: var(--bs-border-width);
    border-radius: var(--bs-border-radius);
    border-style: var(--bs-border-style);
}

.select-items div {
    color: var(--bs-body-color);
    padding: 8px 12px;
    cursor: pointer;
}

.select-items {
    position: absolute;
    background-color: var(--bs-body-bg);
    top: 100%;
    left: 0;
    right: 0;
    z-index: 99;
    margin-top: 6px;
    height: 300px;
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

.select-hide {
    display: none;
}

.select-items div:first-child {
    border-top-left-radius: var(--bs-border-radius);
    border-top-right-radius: var(--bs-border-radius);
}

.select-items div:last-child {
    border-bottom-left-radius: var(--bs-border-radius);
    border-bottom-right-radius: var(--bs-border-radius);
}

.select-items div:hover, .same-as-selected {
    background-color: var(--bs-primary);
    color: var(--bs-white) !important;
}
CSS;
    }

    public static function onLoadJs($id, $options)
    {
        return '_formsjs_bootstrapCustomSelectField';
    }

    public static function onLoadJsData($id, $options)
    {
        return json_encode([
            // 'btn' => $options['btn'] ?? 'btn-outline-primary',
        ]);
    }

    public static function js($id, $options)
    {
        return <<<JS
        _formsjs_bootstrapCustomSelectField = function (element) {
            if (! element.getAttribute('data-formsjs-rendered')) {
                let _id = element.getAttribute('id');
                let _config = JSON.parse(element.getAttribute('data-formsjs-onload-data'));

                var wrapper = document.createElement('div');
                    wrapper.setAttribute('class', 'bs-select');
                    element.parentNode.insertBefore(wrapper, element);
                    wrapper.appendChild(element);

                var x, i, j, l, ll, selElmnt, a, b, c;
                    selElmnt = element;
                    ll = selElmnt.length;

                    a = document.createElement("DIV");
                    a.setAttribute("class", "select-selected");
                    a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
                    wrapper.appendChild(a);

                    /* For each element, create a new DIV that will contain the option list: */
                    b = document.createElement("DIV");
                    b.setAttribute("class", "select-items select-hide");

                for (j = 0; j < ll; j++) {
                    c = document.createElement("DIV");
                    c.innerHTML = selElmnt.options[j].innerHTML;

                    if (selElmnt.options[j].value) {
                        c.setAttribute("data-value", selElmnt.options[j].value);
                    } else {
                        c.setAttribute("data-value", null);
                    }

                    if (selElmnt.options[j].value == selElmnt.value) {
                        a.innerHTML = selElmnt.options[j].innerHTML;
                        c.setAttribute("class", "same-as-selected");
                    }

                    c.addEventListener("click", function(e) {
                        var y, i, k, s, h, sl, yl;
                        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                        sl = s.length;
                        h = this.parentNode.previousSibling;
                        for (i = 0; i < sl; i++) {
                            if (s.options[i].innerHTML == this.innerHTML) {
                                s.selectedIndex = i;
                                h.innerHTML = this.innerHTML;
                                y = this.parentNode.getElementsByClassName("same-as-selected");
                                yl = y.length;
                                for (k = 0; k < yl; k++) {
                                    y[k].removeAttribute("class");
                                }
                                this.setAttribute("class", "same-as-selected");
                                break;
                            }
                        }
                        element.selected = this.getAttribute('data-value');
                        var event = new Event('change', {
                            bubbles: true
                        });
                        element.dispatchEvent(event);
                        h.click();
                    });
                    b.appendChild(c);
                    wrapper.appendChild(b);
                }

                a.addEventListener("click", function(e) {
                    e.stopPropagation();
                    _formsjs_closeAllBsSelect(this);
                    this.nextSibling.classList.toggle("select-hide");
                    this.classList.toggle("select-arrow-active");
                });

                function _formsjs_closeAllBsSelect(elmnt) {
                    var x, y, i, xl, yl, arrNo = [];
                    x = document.getElementsByClassName("select-items");
                    y = document.getElementsByClassName("select-selected");
                    xl = x.length;
                    yl = y.length;
                    for (i = 0; i < yl; i++) {
                        if (elmnt == y[i]) {
                            arrNo.push(i)
                        } else {
                            y[i].classList.remove("select-arrow-active");
                        }
                    }
                    for (i = 0; i < xl; i++) {
                        if (arrNo.indexOf(i)) {
                            x[i].classList.add("select-hide");
                        }
                    }
                }

                document.addEventListener("click", _formsjs_closeAllBsSelect);
                document.addEventListener("keyup", function (e) {
                    if (e.key === 'Escape') {
                        _formsjs_closeAllBsSelect();
                    }
                });
            }
        }
JS;
    }
}
