<?php

namespace Grafite\Forms\Fields;

use Grafite\Forms\Fields\Field;

class AutoSuggest extends Field
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

    /**
     * Input attributes
     *
     * @return array
     */
    protected static function getAttributes()
    {
        return [];
    }

    /**
     * View path for a custom template
     *
     * @return mixed
     */
    protected static function getView()
    {
        return null;
    }

    /**
     * Field related scripts
     *
     * @param array $options
     * @return array
     */
    public static function scripts($options)
    {
        return [];
    }

    /**
     * Field related JavaScript
     *
     * @param string $id
     * @param array $options
     * @return string|null
     */
    public static function js($id, $options)
    {
        foreach ($options as $key => $option) {
            if (! in_array($key, static::getFieldOptions())) {
                $options['options'][$key] = $option;
            }
        }

        $items = json_encode(array_values($options['options']));

        return <<<scripts
function _formAutocompleteField(inp, arr) {
    inp.autocomplete = "off";
    var currentFocus;

    inp.addEventListener("focus", function (e) {
        formCloseAllLists();
        _{$id}_formDisplaySelection(this, this.value);
    });

    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        formCloseAllLists();
        _{$id}_formDisplaySelection(this, this.value);
    });

    function formCloseAllLists(elmnt) {
        var x = document.getElementsByClassName("form-autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }

    function _{$id}_formDisplaySelection(_field, val) {
        currentFocus = -1;

        let _formElementStyle = getComputedStyle(_field);
        /*create a DIV element that will contain the items (values):*/
        let a = document.createElement("DIV");
        a.setAttribute("id", _field.id + "form-autocomplete-list");
        a.setAttribute("class", "form-autocomplete-items rounded");
        let border = "border: " + _formElementStyle.border + "; ";
        let width = "width:"+ inp.offsetWidth + "px; ";
        a.setAttribute("style", width + border);

        /*append the DIV element as a child of the autocomplete container:*/
        _field.parentNode.appendChild(a);

        for (i = 0; i < arr.length; i++) {
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                let b = document.createElement("DIV");
                let background = "background-color: " + _formElementStyle.backgroundColor + "; ";
                b.setAttribute('style', background);
                b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arr[i].substr(val.length);
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                b.addEventListener("click", function(e) {
                    inp.value = this.getElementsByTagName("input")[0].value;
                    formCloseAllLists();
                });

                a.appendChild(b);
            }
        }

        if (a.childNodes.length === 0) {
            a.style.display = 'none';
        }
    }

    document.addEventListener("click", function (e) {
        formCloseAllLists(e.target);
    });

    function addActive(x) {
        if (!x) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        x[currentFocus].classList.add("form-autocomplete-active");
        x[currentFocus].parentNode.scrollTop = currentFocus * 48;
    }

    function removeActive(x) {
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("form-autocomplete-active");
        }
    }

    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "form-autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            currentFocus++;
            addActive(x);
        } else if (e.keyCode == 38) { //up
            currentFocus--;
            addActive(x);
        } else if (e.keyCode == 13) {
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
        }
    });
}

_formAutocompleteField(document.getElementById("{$id}"), {$items});

scripts;
    }

    /**
     * Field related stylesheets
     *
     * @param array $options
     * @return array
     */
    public static function stylesheets($options)
    {
        return [];
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
        return <<<styles
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
styles;
    }
}
