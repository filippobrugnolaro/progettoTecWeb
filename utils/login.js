var validationDetails = {
    "email" : ["Nome del personaggio",/^[A-Za-z\s]{2,}$/,"Inserire almeno 2 caratteri"],
    "password": ["Colore del personaggio",/^[A-Za-z\s]{2,}$/,"Inserire almeno 2 caratteri"],
}

function showError(input) {
    var parent = input.parentNode;
    var message = validationDetails[input.id][2];

    var error = document.createElement("strong");
    error.className = "errSuggestion";
    error.appendChild(document.createTextNode(message));

    parent.appendChild(error);
}

function fieldValidation(input, event = null) {
    removeErrorMessage(input);

    if(input.value.search(validationDetails[input.id][1]) != 0
        || input.value == validationDetails[input.id][0]) {
        showError(input);
        input.focus(); //focus on error (ok 4 users & SR)
        input.select(); //select all chars
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

		function formValidation(event) {

			var ret = true;
			var focus = null;
			for(var key in validationDetails) {
				var input = document.getElementById(key);
				var validation = fieldValidation(input,event);

				//console.log("ret = " + ret + "; validation = " + validation + "; focus = " + focus);

				if(focus == null && ret == true && validation == false)
					focus = input;

				ret = ret && validation;
			}

			if(ret == false)
				focus.focus();

			return ret;
		}