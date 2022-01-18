var validationDetails = {
    "cognomeUser"   : ["Cognome dell'utente",/^[A-Za-zàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
    "nomeUser"      : ["Nome dell'utente",/^[A-Za-zàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
    "nascitaUser"   : ["Data di nascita dell'utente",/^\d{4}-\d{2}-\d{2}$/,"Inserire una data antecedente a quella odierna"],
    "cfUser"        : ["Codice fiscale dell'utente",/^(?:[A-Z][AEIOU][AEIOUX]|[AEIOU]X{2}|[B-DF-HJ-NP-TV-Z]{2}[A-Z]){2}(?:[\dLMNP-V]{2}(?:[A-EHLMPR-T](?:[04LQ][1-9MNP-V]|[15MR][\dLMNP-V]|[26NS][0-8LMNP-U])|[DHPS][37PT][0L]|[ACELMRT][37PT][01LM]|[AC-EHLMPR-T][26NS][9V])|(?:[02468LNQSU][048LQU]|[13579MPRTV][26NS])B[26NS][9V])(?:[A-MZ][1-9MNP-V][\dLMNP-V]{2}|[A-M][0L](?:[1-9MNP-V][\dLMNP-V]|[0L][1-9MNP-V]))[A-Z]$/,"Inserire un codice fiscale valido"],
    "telUser"       : ["Numero di telefono dell'utente",/^\d{8,10}$/,"Inserire un numero di telefono valido tra le 8 e le 10 cifre"],
    "email"         : ["E-mail del contatto",/^([\w\-\+\.]+)\@([\w\-\+\.]+)\.([\w\-\+\.]+)$/,"Inserire un indirizzo e-mail corretto"],
    "pswUser"       : ["Password nuova dell'utente",/^.{1,}$/,"Inserire almeno un carattere"],
    "pswCheck"      : ["Verifica password dell'utente",/^.{1,}$/,"La nuova password e la sua verifica non coincidono"],
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
    switch(validationDetails[input.id][0]){
        case "nascitaUser" : return /* input.value.search(validationDetails[input.id][1]) != 0 && */ checkDate(input);

        case "pswCheck" : return /* input.value.search(validationDetails[input.id][1]) != 0 && */ checkReinsert(input);

        default :
        if(input.value.search(validationDetails[input.id][1]) != 0 || input.value == validationDetails[input.id][0]) {
            showError(input);
            return false;
        } else {
            return true;
        }
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

function checkReinsert(input) {
    if(input.value === document.getElementById("newPsw").value){
        return true;
    } else {
        showError(input);
        return false;
        
    }
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