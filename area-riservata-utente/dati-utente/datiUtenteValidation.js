var validationDetails = {
    "cognomeUser"   : ["Cognome dell'utente",/^[A-Za-zàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
    "nomeUser"      : ["Nome dell'utente",/^[A-Za-zàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
    "nascitaUser"   : ["Data di nascita dell'utente",/^\d{4}-\d{2}-\d{2}$/,"Inserire una data antecedente a quella odierna"],
    "telUser"       : ["Numero di telefono dell'utente",/^\d{8,10}$/,"Inserire un numero di telefono valido tra le 8 e le 10 cifre"],
    "oldPsw"        : ["Password vecchia dell'utente",/^.{1,}$/,"Inserire almeno un carattere"],
    "newPsw"        : ["Password nuova dell'utente",/^.{1,}$/,"Password inserita uguale alla precedente"],
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

        case "newPsw" : return /* input.value.search(validationDetails[input.id][1]) != 0 && */ checkPswNewOld(input);

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

function checkPswNewOld(input) {
    if(input.value !== document.getElementById("oldPsw").value){
        return true;
    } else {
        showError(input);
        return false;
    }
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