var validationDetails = {
    "cognomeUser": [/^[A-Za-zàèùìòé'\s]{2,}$/, "Inserire almeno 2 caratteri (numeri e caratteri speciali non ammessi)"],
    "nomeUser": [/^[A-Za-zàèùìòé'\s]{2,}$/, "Inserire almeno 2 caratteri (numeri e caratteri speciali non ammessi)"],
    "nascitaUser": [/^\d{4}-\d{2}-\d{2}$/, "Inserire una data corretta antecedente a quella odierna"],
    "cfUser": [/^(?:[A-Z][AEIOU][AEIOUX]|[AEIOU]X{2}|[B-DF-HJ-NP-TV-Z]{2}[A-Z]){2}(?:[\dLMNP-V]{2}(?:[A-EHLMPR-T](?:[04LQ][1-9MNP-V]|[15MR][\dLMNP-V]|[26NS][0-8LMNP-U])|[DHPS][37PT][0L]|[ACELMRT][37PT][01LM]|[AC-EHLMPR-T][26NS][9V])|(?:[02468LNQSU][048LQU]|[13579MPRTV][26NS])B[26NS][9V])(?:[A-MZ][1-9MNP-V][\dLMNP-V]{2}|[A-M][0L](?:[1-9MNP-V][\dLMNP-V]|[0L][1-9MNP-V]))[A-Z]$/, "Inserire un codice fiscale valido"],
    "telUser": [/^\d{8,10}$/, "Inserire un numero di telefono valido tra le 8 e le 10 cifre"],
    "emailUser": [/^([\w\-\+\.]+)\@([\w\-\+\.]+)\.([\w\-\+\.]+)$/, "Inserire un indirizzo e-mail corretto"],
    "pswUser": [/^.{1,}$/, "Inserire almeno un carattere"],
    "pswCheck": [/^.{1,}$/, "La nuova password e la sua verifica non coincidono"],
    "username": [/^(?=.{4,10}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9]+(?<![_.])$/, "L'username deve contenere tra 4 e 10 caratteri (solo lettere minuscole e numeri ammessi)"],
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

function fieldValidation(input, event = null) {
    removeErrorMessage(input);

    switch (input.id) {
        case "nascitaUser":
            if ((event !== null && !checkDate(input)) || (event === null && (input.value.length > 0 && !checkDate(input)))) {
                showError(input);
                return false;
            } else {
                return true;
            }

        case "pswCheck":
            if ((event !== null && !checkReinsert(input))
                || (event === null && (document.getElementById("pswUser").value.length > 0 && !checkReinsert(input)))) {
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
}

function formValidation(event) {

    var ret = true;
    var focus = null;

    for (var key in validationDetails) {
        var input = document.getElementById(key);
        var validation = fieldValidation(input, event);

        //console.log("ret = " + ret + "; validation = " + validation + "; focus = " + focus);

        if (focus == null && ret == true && validation == false)
            focus = input;

        ret = ret && validation;
    }

    if (ret == false)
        focus.focus();

    return ret;
}

function checkReinsert(input) {
    if(input.value.search(validationDetails[input.id][0]) != 0)
        return false;

    return input.value === document.getElementById("pswUser").value ? true : false;
}

function checkDate(input) {
    if(input.value.search(validationDetails[input.id][0]) != 0)
        return false;

    var birthDate = new Date(input.value);
    var todayDate = new Date();

    return birthDate <= todayDate ? true : false;
}