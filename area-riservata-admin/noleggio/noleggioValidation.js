var validationDetails = {
    "marca"         : ["Marca della moto",/^[\wàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
    "modello"       : ["Modello della moto",/^[\wàèùìòé\d\s] {2,}$/,"Inserire almeno 2 fra caratteri e numeri"],
    "anno"          : ["Nome dell'istruttore",/^\d{4}$/,"Inserire un numero di 4 cifre maggiore di 2000 e non superiore all'anno attuale"],
}

function showError(input) {
    var parent = input.parentNode;
    var message = validationDetails[input.id][2];
    var error = document.createElement("strong");
    error.className = "errSuggestion";
    error.appendChild(document.createTextNode(message));

    parent.appendChild(error);
}

function fieldValidation(input) {
    removeErrorMessage(input);
        if(input.value.search(validationDetails[input.id][1]) != 0 || input.value == validationDetails[input.id][0]) {
            showError(input);
            return false;
        } else {
            return true;
    }
}

function removeErrorMessage(input) {
    var parent = input.parentNode;
    if(parent.children.length >= 2) {
        parent.removeChild(parent.children[1]);
    }
}

function load() {
    for(var key in validationDetails) {
        var input = document.getElementById(key);
        input.onfocus = function() {removeErrorMessage(this)}; //prepare field
        input.onblur = function() {fieldValidation(this)}; //validate field
    }
}

function formValidation() {
    var ret = true;
    for(var key in validationDetails) {
        var input = document.getElementById(key);
        ret = ret & input.onblur();
    }
    return ret;
}

function checkYear(input) {
    var today = new Date()
    if(input.value < today.getFullYear() || input.value > 2000) {
        return true;
    } else {
        showError(input);
        return false;
    }
}