var validationDetails = {
    "data"          : [/^\d{4}-\d{2}-\d{2}$/,"Inserire una data valida successiva ad oggi"],
    "posti"         : [/^[0-9]{2,3}$/,"Inserire un numero compreso tra 50 e 200 inclusi"],
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
        case "data":
            if ((event !== null && !checkDate(input))
				|| (event === null && (input.value.length > 0 && !checkDate(input)))) {
                showError(input);
                return false;
            } else {
                return true;
            }

        case "posti":
            if ((event !== null && !checkPosti(input))
                || (event === null && (input.value.length > 0 && !checkPosti(input)))) {
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

function checkDate(input) {
	if(input.value.search(validationDetails[input.id][0]) != 0)
		return false;

	var selectedDate = new Date(input.value);
	var todayDate = new Date();

	return selectedDate > todayDate ? true : false;
}

function checkPosti(input) {
	if(input.value.search(validationDetails[input.id][0]) != 0)
			return false;

    return (input.value >= 50 && input.value <= 200) ? true : false;
}