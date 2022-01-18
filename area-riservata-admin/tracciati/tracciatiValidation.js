var validationDetails = {
    "lunghezza"     : ["Lunghezza del tracciato",/^[0-9]+$/,"Inserire un numero tra 10 e 10000 inclusi multiplo di 10"],
    "descrizione"   : ["Descrizione del tracciato",/^.{30,200}$/,"Inserire tra i 30 e i 200 caratteri"],
    "apertura"      : ["Orario d'apertura del tracciato",/^\d{2}:\d{2}$/,"Inserire un'orario compreso tra le 08:00 e le 14:00"],
    "chiusura"      : ["Orario di chiusura del tracciato",/^\d{2}:\d{2}$/,"Inserire un'orario compreso tra le 14:00 e le 20:00"]
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

function checkApertura(input) {
    if(String(input.value) >= "08:00" && String(input.value) <= "14:00") {
        return true;
    } else {
        showError(input);
        return false;
    }
}

function checkChiusura(input) {
    if(String(input.value) >= "14:00" && String(input.value) <= "20:00") {
        return true;
    } else {
        showError(input);
        return false;
    }
}

function checkLunghezza(input) {
    if(input.value >= 10 && input.value <= 20000 && !(input.value % 10)){
        return true;
    } else {
        showError(input);
        return false;        
    }
}