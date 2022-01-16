var validationDetails = {
    "marca"         : ["Marca della moto",/^[\wàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
    "modello"       : ["Modello della moto",/^[\wàèùìòé\s] {2,}$/,"Inserire almeno 2 caratteri"],
    "anno"          : ["Nome dell'istruttore",/^\d{4}$/,"Inserire almeno 2 caratteri"],
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
            //input.focus(); //focus on error (ok 4 users & SR)
            //input.select(); //select all chars
            return false;
        } else {
            return true;
        }
}

function removeErrorMessage(input) {
    var parent = input.parentNode;
    if(parent.children.length >= 2)
        parent.removeChild(parent.children[1]);
}

function load() {
    for(var key in validationDetails) {
        var input = document.getElementById(key);
        setDefault(input); //set placeholder
        input.onfocus = function() {fieldForInput(this)}; //prepare field
        input.onblur = function() {fieldValidation(this)}; //validate field
    }
}

function setDefault(input) {
    if(input.value == "") {
        input.value = validationDetails[input.id][0];
        input.className = "defaultText";
    }
}

function fieldForInput(input) {
    if(input.value == validationDetails[input.id][0]) {
        input.value = "";
        input.className = "";
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