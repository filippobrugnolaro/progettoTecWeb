function setup() {
    var moto = document.getElementById('motoNol');
    var id = document.getElementById('corso');
    var descCorso = document.getElementById('descCorso');

    id.onchange = function () {
        getDirtBikes(moto, id.value);
        getDescCorso(descCorso, id.value);
        enDisSelectMoto(checkboxMoto.checked);
    }

    var checkboxMoto = document.getElementById('moto');
    checkboxMoto.onchange = function() {enDisSelectMoto(checkboxMoto.checked)};

    getDirtBikes(moto, id.value);
    getDescCorso(descCorso, id.value);
    enDisSelectMoto(checkboxMoto.checked);
}

window.onload = setup;

function enDisSelectMoto(checkBox) {
    if(checkBox == false)
        document.getElementById('motoNol').disabled = true;
    else
        document.getElementById('motoNol').disabled = false;
}

function getDirtBikes(select, id) {
    if (id != "") {
        document.getElementById('moto').disabled = false;
        document.getElementById('motoNol').disabled = false;
        document.getElementById('vestiario').disabled = false;
        document.getElementById('hint').innerHTML = "";

        var xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                if (xmlhttp.status == 200) {
                    var motos = JSON.parse(this.responseText);

                    var i, L = select.options.length - 1;
                    for(i = L; i >= 0; i--) {
                       select.remove(i);
                    }


                    if(motos != null) {
                        for (moto of motos){
                            var opt = document.createElement('option');
                            opt.value = moto.numero;
                            opt.innerHTML = moto.marca + " " + moto.modello + " " + moto.anno;
                            select.appendChild(opt);
                        }
                    } else {
                        document.getElementById('moto').disabled = true;
                        document.getElementById('motoNol').disabled = true;
                        document.getElementById('vestiario').disabled = true;
                        document.getElementById('hint').innerHTML = "Non ci sono più moto disponibili per questa giornata!";
                    }
                }
            }
        };

        xmlhttp.open("GET", "getDirtBikesAvailable.php?id=" + id, true);
        xmlhttp.send();
    }
}

function getDescCorso(descCorso, id) {
    if (id != "") {
        var xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                if (xmlhttp.status == 200) {
                    var corso = JSON.parse(this.responseText);
                    corso = corso[0];

                    descCorso.innerHTML = corso.descrizione;
                }
            }
        };

        xmlhttp.open("GET", "getDescCorso.php?id=" + id, true);
        xmlhttp.send();
    }
}