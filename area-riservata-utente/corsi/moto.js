window.onload = function() {
    var moto = document.getElementById('motoNoleggio');
    var id = document.getElementById('corso');
    id.onchange = getDirtBikes(moto,id.value);

    getDirtBikes(moto,id.value);
}


function getDirtBikes(select,id) {
    if(id != "") {
        var xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                if (xmlhttp.status == 200) {
                        var motos = JSON.parse(this.responseText);

                        var options = document.getElementById('motoNoleggio');
                        var option = options.getElementsByTagName('option');

                        for(var i=0; i<option.length; i++) {
                                option.removeChild(options[i]);
                                i--; //perdono un elemento ogni volta!!
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

        xmlhttp.open("GET", "getDirtBikesAvailable.php?id=" + id, true);
        xmlhttp.send();
    }
}