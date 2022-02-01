var validationDetails = {
    "lunghezza"     : [/^[0-9]{3,5}$/,"Inserire un numero tra 1.000 e 10.000"],
    "descrizione"   : [/^.{30,300}$/,"Inserire tra i 30 e i 300 caratteri"],
    "apertura"      : [/^\d{2}:\d{2}$/,"Inserire un'orario compreso tra le 08:00 e le 14:00"],
    "chiusura"      : [/^\d{2}:\d{2}$/,"Inserire un'orario compreso tra le 14:00 e le 20:00"]
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
        case "lunghezza":
            if ((event !== null && !checkLunghezza(input))
				|| (event === null && (input.value.length > 0 && !checkLunghezza(input)))) {
                showError(input);
                return false;
            } else {
                return true;
            }

        case "apertura":
            if ((event !== null && !checkApertura(input))
                || (event === null && (input.value.length > 0 && !checkApertura(input)))) {
                showError(input);
                return false;
            } else {
                return true;
            }

		case "chiusura":
			if ((event !== null && !checkChiusura(input))
				|| (event === null && (input.value.length > 0 && !checkChiusura(input)))) {
				showError(input);
				return false;
			} else {
				return true;
			}

        default:
            if ((event !== null && input.value.search(validationDetails[input.id][0]) != 0)
                || (event === null && (input.value.length > 0 && input.value.search(validationDetails[input.id][1]) != 0))) {
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

        if (focus == null && ret == true && validation == false)
            focus = input;

        ret = ret && validation;
    }

    if (ret == false)
        focus.focus();

    return ret;
}

function checkApertura(input) {
	if(input.value.substring(0,5).search(validationDetails[input.id][0]) != 0)
		return false;

    if(input.value >= "08:00" && input.value <= "14:00") {
        return true;
    } else {
        return false;
    }
}

function checkChiusura(input) {
	if(input.value.substring(0,5).search(validationDetails[input.id][0]) != 0)
		return false;

    if(input.value >= "14:00" && input.value <= "20:00") {
        return true;
    } else {
        return false;
    }
}

function checkLunghezza(input) {
	if(input.value.search(validationDetails[input.id][0]) != 0)
		return false;

    if(input.value >= 500 && input.value <= 10000){
        return true;
    } else {
        return false;
    }
}