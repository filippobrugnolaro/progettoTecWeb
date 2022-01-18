var validationDetails = {
    "posti"         : ["Numero di posti disponibili",/^[0-9]+$/,"Inserire un numero compreso tra 2 e 15 inclusi"],
    "descrizione"   : ["Descrizione del corso",/^.{30,200}$/,"Inserire almeno tra i 30 e i 200 caratteri"],
    "istruttore"    : ["Nome dell'istruttore",/^[A-Za-zàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"]
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

function checkPosti(input) {
    if((input.value >= 2) && (input.value <= 15)){
        return true;
    } else {
        showError(input);
        return false;
    }
}