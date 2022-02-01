var validationDetails = {
    "cognomeUser": [/^[A-Za-zàèùìòé'\s]{2,}$/, "Inserire almeno 2 caratteri (numeri e caratteri speciali non ammessi)"],
    "nomeUser": [/^[A-Za-zàèùìòé'\s]{2,}$/, "Inserire almeno 2 caratteri (numeri e caratteri speciali non ammessi)"],
    "nascitaUser": [/^\d{4}-\d{2}-\d{2}$/, "Inserire una data corretta antecedente a quella odierna"],
    "telUser": [/^\d{8,10}$/, "Inserire un numero di telefono valido tra le 8 e le 10 cifre"]
}

var validationDetails2 = {
    "oldPsw": [/^.{1,}$/, "Inserire almeno un carattere"],
    "newPsw": [/^.{1,}$/, "Inserire una password di almeno un carattere diversa dalla precedente"],
    "pswCheck": [/^.{1,}$/, "La nuova password e la sua verifica non coincidono"]
}


function showError(input) {
    var parent = input.parentNode;
    var message = validationDetails[input.id][1];
    var error = document.createElement("strong");
    input.setAttribute('aria-invalid', 'true');
    input.setAttribute('aria-describedby', input.id + '-error');
    error.id = input.id + '-error';

    error.className = "errSuggestion";
    error.appendChild(document.createTextNode(message));
    parent.appendChild(error);
}

function showError2(input) {
    var parent = input.parentNode;
    var message = validationDetails2[input.id][1];
    var error = document.createElement("strong");
    input.setAttribute('aria-invalid', 'true');
    input.setAttribute('aria-describedby', input.id + '-error');
    error.id = input.id + '-error';

    error.className = "errSuggestion";
    error.appendChild(document.createTextNode(message));
    parent.appendChild(error);
}

function fieldValidation(input, event = null) {
    removeErrorMessage(input);

    switch (input.id) {
        case "nascitaUser":
            if ((event !== null && !checkDate(input))
                || (event === null && (input.value.length > 0 && !checkDate(input)))) {
                showError(input);
                return false;
            } else {
                return true;
            }

        default:
            if ((event !== null && input.value.search(validationDetails[input.id][0]) != 0)
                || (event === null && (input.value.length > 0 && input.value.search(validationDetails[input.id][0]) != 0))) {
                showError(input);
                return false;
            } else {
                return true;
            }
    }
}

function fieldValidation2(input, event = null) {
    removeErrorMessage(input);

    switch (input.id) {
        case "newPsw":
            if ((event !== null && !checkPswNewOld(input))
                || (event === null && (document.getElementById("newPsw").value.length > 0 && !checkPswNewOld(input)))) {
                showError2(input);
                return false;
            } else {
                return true;
            }

        case "pswCheck":
            if ((event !== null && !checkReinsert(input))
                || (event === null && (document.getElementById("newPsw").value.length > 0 && !checkReinsert(input)))) {
                showError2(input);
                return false;
            } else {
                return true;
            }

        default:
            if ((event !== null && input.value.search(validationDetails2[input.id][0]) != 0)
                || (event === null && (input.value.length > 0 && input.value.search(validationDetails2[input.id][0]) != 0))) {
                showError2(input);
                return false;
            } else {
                return true;
            }
    }
}

function removeErrorMessage(input) {
    input.removeAttribute('aria-invalid');
    input.removeAttribute('aria-describedby');
    var parent = input.parentNode;

    if(parent.children.length >= 2) {
        parent.removeChild(parent.children[1]);
    }
}

function load() {
    for (var key in validationDetails) {
        var input = document.getElementById(key);
        input.onblur = function () { fieldValidation(this) }; //validate field
    }

    for (var key in validationDetails2) {
        var input = document.getElementById(key);
        input.onblur = function () { fieldValidation2(this) }; //validate field
    }
}

function formValidation(event) {

    var ret = true;
    var focus = null;

    for (var key in validationDetails) {
        var input = document.getElementById(key);
        var validation = fieldValidation(input, event);

        if (focus == null && ret == true && validation == false)
            focus = input;

        ret = ret && validation;
    }

    if (ret == false) {
        focus.focus();
    }

    return ret;
}

function formValidation2(event) {
    var ret = true;
    var focus = null;

    for (var key in validationDetails2) {
        var input = document.getElementById(key);
        var validation = fieldValidation2(input, event);

        if (focus == null && ret == true && validation == false)
            focus = input;

        ret = ret && validation;
    }

    if (ret == false) {
        focus.focus();
    }

    return ret;
}

function checkReinsert(input) {
    if(input.value.search(validationDetails2[input.id][0]) != 0)
        return false;

    return input.value === document.getElementById("newPsw").value ? true : false;
}

function checkDate(input) {
    if(input.value.search(validationDetails[input.id][0]) != 0)
        return false;

    var birthDate = new Date(input.value);
    var todayDate = new Date();

    return birthDate <= todayDate ? true : false;
}

function checkPswNewOld(input) {
    if(input.value.search(validationDetails2[input.id][0]) != 0)
        return false;

    return input.value !== document.getElementById("oldPsw").value ? true : false;
}