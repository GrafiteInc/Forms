document.querySelectorAll('[data-formsjs-onload]').forEach(function (element) {
    if (! element.getAttribute('data-formsjs-rendered')) {
        let _method = element.getAttribute('data-formsjs-onload');
            window[_method](element);
            element.setAttribute('data-formsjs-rendered', true);
    }
});

document.querySelectorAll('[data-formsjs-onchange]').forEach(function (element) {
    if (! element.getAttribute('data-formsjs-rendered')) {
        let _method = element.getAttribute('data-formsjs-onchange');
            _method = _method.replace('(event)', '');

            element.addEventListener('change', function (event) {
                window[_method](event);
            });
            element.setAttribute('data-formsjs-rendered', true);
    }
});

document.querySelectorAll('[data-formsjs-onclick]').forEach(function (element) {
    if (! element.getAttribute('data-formsjs-rendered')) {
        let _method = element.getAttribute('data-formsjs-onclick');
            _method = _method.replace('(event)', '');
        element.addEventListener('click', function (event) {
            event.preventDefault();
            _method = _method.replace('return ', '');
            _method = _method.replace('window.', '');

            if (_method.includes('Forms_validate_submission')) {
                window.Forms_validate_submission(event.target.form, '<i class=\"fas fa-circle-notch fa-spin mr-2\"></i>',event.target);
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
        });
        element.setAttribute('data-formsjs-rendered', true);
    }
});
