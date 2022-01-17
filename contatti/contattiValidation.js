var validationDetails = {
			"nome"          : ["Nome del contatto",/^[A-Za-zàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
			"cognome"       : ["Cognome del contatto",/^[A-Za-zàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
			"email"        : ["E-mail del contatto",/^([\w\-\+\.]+)\@([\w\-\+\.]+)\.([\w\-\+\.]+)$/,"Inserire un indirizzo e-mail corretto"],
			"telefono"      : ["Numero di telefono del contatto",/^\d{8,10}$/,"Inserire un numero di telefono valido"],
            "oggetto"       : ["Oggetto del messaggio",/^[A-Za-zàèùìòé\s]{2,}$/,"Inserire almeno 2 caratteri"],
            "messaggio"   : ["Descrizione del messaggio",/^.{10,}$/,"Inserire almeno 10 caratteri"],
            "termini"       : ["Accettazione termini e informativa",null,"Accettare i termini e l'informativa"]
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
			if(input.type !== "checkbox"){
                if(input.value.search(validationDetails[input.id][1]) != 0 || input.value == validationDetails[input.id][0]) {
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