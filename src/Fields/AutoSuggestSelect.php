<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class AutoSuggestSelect extends Field
{
    /**
     * Input type
     *
     * @return string
     */
    protected static function getType()
    {
        return 'text';
    }

    public static function onLoadJs($id, $options)
    {
        return "_formsjs_autosuggestSelectField";
    }

    public static function onLoadJsData($id, $options)
    {
        foreach ($options as $key => $option) {
            if (! in_array($key, static::getFieldOptions())) {
                $options['options'][$key] = $option;
            }
        }

        return json_encode($options['options']);
    }

    public static function js($id, $options)
    {
        return <<<JS
            window._formsjs_autosuggestSelectField = function (inp) {
                if (! inp.getAttribute('data-formsjs-rendered')) {
                    let arr = JSON.parse(inp.getAttribute('data-formsjs-onload-data'));
                    inp.type = "hidden";
                    var currentFocus;

                    let _altInput = inp.cloneNode(true);
                        _altInput.id = inp.id + "_visual";
                        _altInput.name = inp.name + "_visual";
                        _altInput.type = "text";

                        if (arr[inp.value]) {
                            _altInput.value = arr[inp.value];
                        }

                        _altInput.autocomplete = "off";

                    inp.parentNode.appendChild(_altInput);

                    _altInput.addEventListener("focusin", function (e) {
                        formCloseAllSelectLists();
                        this.value = '';

                        _formDisplaySelection(this, this.value);

                        setTimeout(function () {
                            _altInput.addEventListener("click", formCloseAllSelectLists);
                        }, 250);
                    });

                    _altInput.addEventListener("input", function(e) {
                        var a, b, i, val = this.value;
                        formCloseAllSelectLists();
                        _formDisplaySelection(this, this.value);
                    });

                    function formCloseAllSelectLists(elmnt) {
                        var x = document.getElementsByClassName("form-autocomplete-items");
                        for (var i = 0; i < x.length; i++) {
                            if (elmnt != x[i] && elmnt != x[i].previousElementSibling) {
                                x[i].parentNode.removeChild(x[i]);
                            }
                        }
                    }

                    function _formDisplaySelection(_field, val) {
                        currentFocus = -1;
                        let _formElementStyle = getComputedStyle(_field);

                        /*create a DIV element that will contain the items (values):*/
                        let a = document.createElement("DIV");
                        a.setAttribute("id", _altInput.id + "form-autocomplete-list");
                        a.setAttribute("class", "form-autocomplete-items rounded");
                        let border = "border: " + _formElementStyle.border + "; ";
                        let width = "width:"+ _altInput.offsetWidth + "px; ";
                        let background = "background-color: " + _formElementStyle.backgroundColor + "; ";
                        a.setAttribute("style", width + border + background);

                        /*append the DIV element as a child of the autocomplete container:*/
                        _altInput.parentNode.appendChild(a);
                        let _itemCount = Object.keys(arr).length;

                        Object.keys(arr).forEach(key => {
                            if (arr[key].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                                let b = document.createElement("DIV");
                                let background = "background-color: " + _formElementStyle.backgroundColor + "; ";
                                b.setAttribute('style', background);
                                b.innerHTML = "<strong>" + arr[key].substr(0, val.length) + "</strong>";
                                b.innerHTML += arr[key].substr(val.length);
                                b.innerHTML += "<input type='hidden' value='" + key + "'>";
                                b.addEventListener("click", function(e) {
                                    inp.value = this.getElementsByTagName("input")[0].value;
                                    _altInput.value = arr[key];
                                    formCloseAllSelectLists();

                                    if (a.childNodes.length > 0) {
                                        _altInput.setAttribute('class', inp.getAttribute('class'));
                                        let _formElementStyle = getComputedStyle(_altInput);
                                        let border = "border: " + _formElementStyle.border + "; ";
                                        let width = "width:"+ _altInput.offsetWidth + "px; ";
                                        a.setAttribute("style", width + border);
                                    }
                                });

                                a.appendChild(b);
                            }
                        });

                        if (a.childNodes.length === 0) {
                            a.style.display = 'none';
                        }
                    };

                    document.addEventListener("click", function (e) {
                        formCloseAllSelectLists(e.target);
                    });

                    _altInput.addEventListener('focusout', function () {
                        if (inp.value === '') {
                            _altInput.value = 'None';
                            inp.value = null;
                        }

                        if (inp.value !== '') {
                            _altInput.value = arr[inp.value];
                        }

                        if (_altInput.value === '') {
                            _altInput.value = 'None';
                            inp.value = null;
                        }

                        setTimeout(function () {
                            formCloseAllSelectLists();
                            _altInput.removeEventListener("click", formCloseAllSelectLists);
                        }, 250);
                    });

                    function addSelectActive(x) {
                        if (!x) return false;
                        removeSelectActive(x);
                        if (currentFocus >= x.length) currentFocus = 0;
                        if (currentFocus < 0) currentFocus = (x.length - 1);
                        x[currentFocus].classList.add("form-autocomplete-active");
                        x[currentFocus].parentNode.scrollTop = currentFocus * 44;
                    }

                    function removeSelectActive(x) {
                        for (var i = 0; i < x.length; i++) {
                            x[i].classList.remove("form-autocomplete-active");
                        }
                    }

                    /*execute a function presses a key on the keyboard:*/
                    _altInput.addEventListener("keydown", function(e) {
                        var x = document.getElementById(this.id + "form-autocomplete-list");
                        if (x) x = x.getElementsByTagName("div");
                        if (e.keyCode == 40) {
                            currentFocus++;
                            addSelectActive(x);
                        } else if (e.keyCode == 38) { //up
                            currentFocus--;
                            addSelectActive(x);
                        } else if (e.keyCode == 13) {
                            e.preventDefault();
                            if (currentFocus > -1) {
                                if (x) x[currentFocus].click();
                            }
                        }

                        let _list = document.getElementById(_altInput.id + "form-autocomplete-list");
                        if (_list && _list.childNodes.length === 0) {
                            _altInput.setAttribute('class', inp.getAttribute('class') + ' is-invalid');
                        }

                        if (_list && _list.childNodes.length > 0) {
                            _altInput.setAttribute('class', inp.getAttribute('class'));
                            let _formElementStyle = getComputedStyle(_altInput);
                            let border = "border: " + _formElementStyle.border + "; ";
                            let width = "width:"+ _altInput.offsetWidth + "px; ";
                            _list.setAttribute("style", width + border);
                        }
                    });
                }
            }
JS;
    }

    /**
     * Field related styles
     *
     * @param string $id
     * @param array $options
     * @return string|null
     */
    public static function styles($id, $options)
    {
        return <<<CSS
.form-autocomplete {
    position: relative;
    display: inline-block;
}

.form-autocomplete-items {
  position: absolute;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  margin-top: 8px;
  max-height: 300px;
  overflow-y: scroll;
}

.form-autocomplete-items div {
  padding: 10px;
  cursor: pointer;
}

.form-autocomplete-items div:hover {
  /*when hovering an item:*/
  background-color: #e9e9e9;
}

.form-autocomplete-active {
  /*when navigating through the items using the arrow keys:*/
  background-color: var(--bs-primary) !important;
  color: #ffffff;
}
CSS;
    }
}
