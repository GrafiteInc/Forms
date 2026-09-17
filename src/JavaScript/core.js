// Resolve a data-formsjs-* handler name from the element or its parent form.
// Returns null when neither declares one.
window._formsjs_resolve_method = function (element, attribute) {
    let _method = element.getAttribute(attribute);

    if (! _method) {
        let _form = element.closest("form");
        _method = _form ? _form.getAttribute(attribute) : null;
    }

    return _method ? _method.replace("(event)", "") : null;
};

// Call a window-level handler only if it exists. Handlers can be missing when
// markup arrives after the FormsJS bundle was compiled (e.g. a Livewire
// re-render introduces a field whose JS was never emitted into the page).
window._formsjs_call_method = function (_method, argument) {
    if (! _method) {
        return false;
    }

    if (typeof window[_method] !== "function") {
        console.warn("FormsJS: handler \"" + _method + "\" is not defined on window", argument);

        return false;
    }

    window[_method](argument);

    return true;
};

window._formsjs_trigger_onchange_function = function (event) {
    let _method = window._formsjs_resolve_method(event.target, "data-formsjs-onchange");

    window._formsjs_call_method(_method, event);
};

window._formsjs_trigger_onkeydown_function = function (event) {
    let _method = window._formsjs_resolve_method(event.target, "data-formsjs-onkeydown");

    window._formsjs_call_method(_method, event);
};

window._formsjs_trigger_onclick_function = function (event) {
    event.preventDefault();

    let _form = event.target.form;
    let _method = event.target.getAttribute("data-formsjs-onclick");

    if (! _method) {
        _form = event.target.parentNode.form;
        _method = event.target.parentNode.getAttribute("data-formsjs-onclick");
    }

    if (_method) {
        _method = _method.replace("(event)", "");
        _method = _method.replace("return ", "");
        _method = _method.replace("window.", "");

        if (_method.includes("FormsJS_validate_submission")) {
            window.FormsJS_validate_submission(_form, "<i class=\"spinner-border spinner-border-sm\"></i>");
        } else if (_method.includes("FormsJS_disableOnSubmit")) {
            window.FormsJS_disableOnSubmit(event);
        } else if (_method.includes(".")) {
            let _path = _method.split(".");

            if (_path.length > 3) {
                throw new Error("Method nesting is too deep. Max of 3!");
            }

            // Walk the path without throwing when an intermediate object is
            // missing, and keep the parent so `this` is preserved on call.
            let _context = window;
            let _fn = _path.reduce(function (obj, key) {
                _context = obj;

                return obj ? obj[key] : undefined;
            }, window);

            if (typeof _fn !== "function") {
                console.warn("FormsJS: handler \"" + _method + "\" is not defined on window", event);

                return;
            }

            _fn.call(_context, event);
        } else if (typeof window[_method] === "function") {
            window[_method](event);
        }
    }
};

window._formsjs_set_bindings = function () {
    document.querySelectorAll("[data-formsjs-onload]").forEach(function (element) {
        if (! element.hasAttribute("data-formsjs-rendered")) {
            let _method = element.getAttribute("data-formsjs-onload");

            // Leave the element unrendered when the handler is missing so a
            // later FormsJS() call can still initialize it.
            if (window._formsjs_call_method(_method, element)) {
                element.setAttribute("data-formsjs-rendered", true);
            }
        }
    });

    document.querySelectorAll("[data-formsjs-onchange]").forEach(function (element) {
        if (! element.hasAttribute("data-formsjs-rendered")) {
            element.addEventListener("change", _formsjs_trigger_onchange_function);
            element.addEventListener("input", _formsjs_trigger_onchange_function);
            element.setAttribute("data-formsjs-rendered", true);
        }
    });

    document.querySelectorAll("[data-formsjs-onkeydown]").forEach(function (element) {
        if (! element.hasAttribute("data-formsjs-rendered")) {
            element.addEventListener("keydown", _formsjs_trigger_onkeydown_function);
            element.setAttribute("data-formsjs-rendered", true);
        }
    });

    document.querySelectorAll("[data-formsjs-onclick]").forEach(function (element) {
        if (! element.hasAttribute("data-formsjs-rendered")) {
            let _newElement = element.cloneNode(true);
            // I don"t like this but it works
            // It resolves a potential issue with Livewire
            element.parentNode.replaceChild(_newElement, element);
            _newElement.addEventListener("click", _formsjs_trigger_onclick_function);
            _newElement.setAttribute("data-formsjs-rendered", true);
        }
    });
};

window._formsjs_set_bindings();
