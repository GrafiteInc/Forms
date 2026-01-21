window.FormsJS_validation = function () {
    let _fields = document.getElementsByClassName("_formValidationClass");

    for (let i = 0; i < _fields.length; i++) {
        _fields[i].addEventListener("keyup", function (e) {
            if (this.value.length > 0) {
                this.classList.remove("_formValidationClass");
                if (this.nextSibling) {
                    this.nextSibling.remove();
                }
            }
        });

        _fields[i].addEventListener("onfocusout", function () {
            if (this.value.length > 0) {
                this.classList.remove("_formValidationClass");
                if (this.nextSibling) {
                    this.nextSibling.remove();
                }
            }
        });

        _fields[i].addEventListener("change", function () {
            if (this.value.length > 0) {
                this.classList.remove("_formValidationClass");
                if (this.nextSibling) {
                    this.nextSibling.remove();
                }
            }
        });
    }
};

window.FormsJS_validation();
