window.onload = function() {
    var moto = document.getElementById('motoNol');
    var data = document.getElementById('dataDisponibile');
    data.onchange = function(){getDirtBikes(moto,data.value)};

    getDirtBikes(moto,data.value);
}


function getDirtBikes(select,data) {
    if(data != "") {
        var xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                if (xmlhttp.status == 200) {
                        var motos = JSON.parse(this.responseText);

                        var i, L = select.options.length - 1;
                        for(i = L; i >= 0; i--) {
                           select.remove(i);
                        }

                        for (moto of motos){
                            var opt = document.createElement('option');
                            opt.value = moto.numero;
                            opt.innerHTML = moto.marca + " " + moto.modello + " " + moto.anno;
                            select.appendChild(opt);
                        }


                }
            }
        };

        xmlhttp.open("GET", "getDirtBikesAvailable.php?data=" + data, true);
        xmlhttp.send();
    }
}