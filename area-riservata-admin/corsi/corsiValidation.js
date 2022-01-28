var validationDetails = {
    "posti"         : [/^[0-9]{1,2}$/,"Inserire un numero compreso tra 2 e 15 inclusi"],
    "descrizione"   : [/^.{30,300}$/,"Inserire almeno tra i 30 e i 300 caratteri"],
    "istruttore"    : [/^[A-Za-zàèùìòé'\s]{2,}$/,"Inserire almeno 2 caratteri (numeri e caratteri speciali non ammessi)"]
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

function checkPosti(input) {
	if(input.value.search(validationDetails[input.id][0]) != 0)
		return false;

    if((input.value >= 2) && (input.value <= 15)){
        return true;
    } else {
        return false;
    }
}