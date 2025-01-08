window.FormsJS_confirm = function (event) {
    let _message = event.target.getAttribute('data-formsjs-confirm-message');

    if (confirm(_message)) {
        event.target.form.submit();
    }
}

window.FormsJS_confirmForAjax = function (event) {
    let _message = event.target.getAttribute('data-formsjs-confirm-message');

    if (confirm(_message)) {
        window['_ajaxMethod'](event);
    }
}

window.FormsJS_submit = function (event) {
    event.target.form.submit()
}

window.FormsJS_disableOnSubmit = function (event) {
    let _target = event.target;

    if (! _target.hasAttribute('data-formsjs-onclick')) {
        _target = event.target.closest('button');
    }

    let _button = _target.getAttribute('data-formsjs-button');

    if (! _button) {
        _button = _target.innerHTML;
    }

    _target.innerHTML = '<i class=\"spinner-border spinner-border-sm me-1\"></i>';
    _target.disabled = true;
    _target.form.submit();
}

window.FormsJS_validate_submission = function (_form, _processing) {
    if (! _form.checkValidity()) {
        let _inputs = _form.querySelectorAll('input');
        let _selects = _form.querySelectorAll('select');
        let _textarea = _form.querySelectorAll('textarea');
        let _inputFields = [..._inputs].concat([..._selects]).concat([..._textarea]);

        _inputFields.forEach(function (_input) {
            if (_input.validity.patternMismatch
                || _input.validity.valueMissing
                || _input.validity.rangeOverflow
                || _input.validity.stepMismatch
                || _input.validity.typeMismatch
                || _input.validity.tooShort
                || _input.validity.tooLong
                || _input.validity.badInput
            ) {
                if (! _input.classList.contains('is-invalid')) {
                    let _errorMessage = document.createElement('div');
                    _errorMessage.classList.add('invalid-feedback');
                    _errorMessage.innerText = _input.validationMessage;

                    _input.classList.add('is-invalid');
                    _input.parentNode.appendChild(_errorMessage);
                    window.FormsJS_validation();
                }
            }
        });

        return false;
    };

    let _button = _form.querySelector('button[type="submit"]');
    let _width = _button.offsetWidth;
    _button.style.width = _width + 'px';
    _button.innerHTML = _processing;

    _form.submit();
};