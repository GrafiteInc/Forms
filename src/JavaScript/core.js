window._formsjs_trigger_onchange_function = function (event) {
    let _method = event.target.getAttribute('data-formsjs-onchange');

    if (! _method) {
        _method = event.target.closest('form').getAttribute('data-formsjs-onchange');
    }

    _method = _method.replace('(event)', '');

    window[_method](event);
}

window._formsjs_trigger_onkeydown_function = function (event) {
    let _method = event.target.getAttribute('data-formsjs-onkeydown');

    if (! _method) {
        _method = event.target.closest('form').getAttribute('data-formsjs-onkeydown');
    }

    _method = _method.replace('(event)', '');

    window[_method](event);
}

window._formsjs_trigger_onclick_function = function (event) {
    event.preventDefault();

    let _form = event.target.form;
    let _method = event.target.getAttribute('data-formsjs-onclick');

    if (! _method) {
        _form = event.target.parentNode.form;
        _method = event.target.parentNode.getAttribute('data-formsjs-onclick');
    }

    if (_method) {
        _method = _method.replace('(event)', '');
        _method = _method.replace('return ', '');
        _method = _method.replace('window.', '');

        if (_method.includes('FormsJS_validate_submission')) {
            window.FormsJS_validate_submission(_form, '<i class=\"spinner-border spinner-border-sm\"></i>');
        } else if (_method.includes('FormsJS_disableOnSubmit')) {
            window.FormsJS_disableOnSubmit(event);
        } else if (_method.includes('.')) {
            let _path = _method.split('.');
            if (_path.length == 2) {
                window[_path[0]][_path[1]](event);
            }

            if (_path.length == 3) {
                window[_path[0]][_path[1]][_path[2]](event);
            }

            if (_path.length == 4) {
                throw new Error('Method nesting is too deep. Max of 3!');
            }
        } else if (typeof window[_method] === 'function') {
            window[_method](event);
        }
    }
}

window._formsjs_set_bindings = function () {
    document.querySelectorAll('[data-formsjs-onload]').forEach(function (element) {
        if (! element.hasAttribute('data-formsjs-rendered')) {
            let _method = element.getAttribute('data-formsjs-onload');
                window[_method](element);
                element.setAttribute('data-formsjs-rendered', true);
        }
    });

    document.querySelectorAll('[data-formsjs-onchange]').forEach(function (element) {
        if (! element.hasAttribute('data-formsjs-rendered')) {
            element.addEventListener('change', _formsjs_trigger_onchange_function);
            element.addEventListener('input', _formsjs_trigger_onchange_function);
            element.setAttribute('data-formsjs-rendered', true);
        }
    });

    document.querySelectorAll('[data-formsjs-onkeydown]').forEach(function (element) {
        if (! element.hasAttribute('data-formsjs-rendered')) {
            element.addEventListener('keydown', _formsjs_trigger_onkeydown_function);
            element.setAttribute('data-formsjs-rendered', true);
        }
    });

    document.querySelectorAll('[data-formsjs-onclick]').forEach(function (element) {
        if (! element.hasAttribute('data-formsjs-rendered')) {
            let _newElement = element.cloneNode(true);
            // I don't like this but it works
            // It resolves a potential issue with Livewire
            element.parentNode.replaceChild(_newElement, element);
            _newElement.addEventListener('click', _formsjs_trigger_onclick_function);
            _newElement.setAttribute('data-formsjs-rendered', true);
        }
    });
}

window._formsjs_set_bindings();
