window.onload = function () {
    var moto = document.getElementById('motoNoleggio');
    var id = document.getElementById('corso');
    var descCorso = document.getElementById('descCorso');

    id.onchange = function () {
        getDirtBikes(moto, id.value);
        getDescCorso(descCorso, id.value);
    }

    getDirtBikes(moto, id.value);
    getDescCorso(descCorso, id.value);
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

                    var options = document.getElementById('motoNol');
                    var option = options.getElementsByTagName('option');

                    for (var i = 0; i < option.length; i++) {
                        option.removeChild(options[i]);
                        i--; //perdono un elemento ogni volta!!
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