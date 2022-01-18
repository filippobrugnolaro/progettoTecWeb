var validationDetails = {
    "data"          : ["Data dell'ingresso",/^\d{4}-\d{2}-\d{2}$/,"Inserire una data valida"],
    "posti"         : ["Numero di posti disponibili",/^[0-9]+$/,"Inserire un numero compreso tra 50 e 200 inclusi"],
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

function checkDate(input) {
    var birthDate = new Date(String(input.value));
    var date = new Date()
    var stringToday = String(date.getFullYear()).concat("-", String(date.getMonth()), "-", String(date.getDay));
    var todayDate = new Date(stringToday);
    if(birthDate < todayDate){
        return true;
    } else {
        showError(input);
        return false;
    }
}

function checkPosti(input) {
    if(input.value >= 50 && input.value <= 200){
        return true;
    } else {
        showError(input);
        return false;
    }
}