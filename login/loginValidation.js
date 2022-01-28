var validationDetails = {
	"username": [/^(?=.{4,10}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9]+(?<![_.])$/, "L'username deve contenere tra 4 e 10 caratteri (solo lettere minuscole e numeri ammessi)"],
	"password"      : [/^.{1,}$/,"Inserire almeno un caratttere"],
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
	if((event !== null && input.value.search(validationDetails[input.id][0]) != 0)
		|| (event === null && (input.value.length > 0 && input.value.search(validationDetails[input.id][0]) != 0))) {
		showError(input);
		return false;
	} else {
		return true;
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
		//input.onfocus = function() {removeErrorMessage(this)}; //prepare field
		input.onblur = function() {fieldValidation(this)}; //validate field
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