var validationDetails = {
	"nome"          : [/^[A-Za-zàèùìòé'\s]{2,}$/,"Inserire almeno 2 caratteri (numeri e caratteri speciali non ammessi)"],
	"cognome"       : [/^[A-Za-zàèùìòé'\s]{2,}$/,"Inserire almeno 2 caratteri (numeri e caratteri speciali non ammessi)"],
	"email"        : [/^([\w\-\+\.]+)\@([\w\-\+\.]+)\.([\w\-\+\.]+)$/,"Inserire un indirizzo e-mail corretto"],
	"telefono"      : [/^\d{8,10}$/,"Inserire un numero di telefono valido tra 8 e 10 cifre"],
	"oggetto"       : [/^.{2,}$/,"Inserire almeno 2 caratteri"],
	"messaggio"   : [/^.{10,}$/,"Inserire almeno 10 caratteri"],
	"termini"       : [null,"Accettare i termini e l'informativa"]
}

function showError(input) {
	var parent = input.parentNode;
	var message = validationDetails[input.id][1];
	var error = document.createElement("strong");
	input.setAttribute('aria-invalid','true');
	input.setAttribute('aria-describedby',input.id + '-error');
	error.id = input.id + '-error';

	error.className = "errSuggestion";
	error.appendChild(document.createTextNode(message));
	parent.appendChild(error);
}

function fieldValidation(input, event = null) {
	removeErrorMessage(input);
	if(input.type !== "checkbox"){
		if((event !== null && input.value.search(validationDetails[input.id][0]) != 0)
			|| (event === null && (input.value.length > 0 && input.value.search(validationDetails[input.id][0]) != 0))) {
			showError(input);
			return false;
		} else {
			return true;
		}
	} else {
		if(input.checked){
			return true;
		} else {
			showError(input);
			return false
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
	for(var key in validationDetails) {
		var input = document.getElementById(key);
		input.onblur = function() {fieldValidation(this)}; //validate field
	}
}

function formValidation(event) {
	var ret = true;
	var focus = null;
	for(var key in validationDetails) {
		var input = document.getElementById(key);
		var validation = fieldValidation(input,event);

		console.log("ret = " + ret + "; validation = " + validation + "; focus = " + focus);

		if(focus == null && ret == true && validation == false)
			focus = input;

		ret = ret && validation;
	}

	if(ret == false) {
		focus.focus();
	}

	console.log(ret == true);

	return ret;
}