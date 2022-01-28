<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');
    require_once('../../utils/moto.php');
    require_once('../../utils/utils.php');

    use DB\dbAccess;
    use MOTO\DirtBike;

    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('gestioneMoto.html');

    $conn = new dbAccess();

    $globalError = "";
    $messaggiForm = "";
    $action = "";
    $actionText = "";

    $id = "";
    $marca = "";
    $modello = "";
    $cilindrata = "";
    $anno = "";

    if (isset($_GET['id'])) {
        //modifica moto
        $action = 'gestioneMoto.php?id=' . $_GET['id'];
        $actionText = "AGGIORNA";

        $idinput = '<label for=\'identificativo\'>Identificativo</label>';
        $idinput .= '<input type=\'text\' readonly name=\'identificativo\' id=\'identificativo\' value=\'_id_\'> <br>';

        $page = str_replace('<id/>',$idinput,$page);

        if (isset($_POST['submit'])) {
            $id = $_GET['id'];

            $marca = sanitizeInputString($_POST['marca']);

            switch(checkInputValidity($marca,'/^[A-Za-zàèùìòé\s]{2,}$/')) {
                case 1: $messaggiForm .= '<li>Marca non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Marca non può contenere numeri o caratteri speciali</li>'; break;
                default: break;
            }

            $modello = sanitizeInputString($_POST['modello']);

            switch(checkInputValidity($modello,'/^[\wàèùìòé\s]{2,}$/')) {
                case 1: $messaggiForm .= '<li>Modello non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Modello non può caratteri speciali</li>'; break;
                default: break;
            }

            $cilindrata = sanitizeInputString($_POST['cilindrata']);

            switch(checkInputValidity($cilindrata,null)) {
                case 1: $messaggiForm .= '<li>Cilindrata non presente.</li>'; break;
                default: break;
            }

            if(!ctype_digit($cilindrata))
                $messaggiForm .= "<li>Cilindrata deve contenere solo numeri.</li>";

            $anno = sanitizeInputString($_POST['anno']);

            switch(checkInputValidity($anno,null)) {
                case 1: $messaggiForm .= '<li>Anno non presente.</li>'; break;
                default: break;
            }

            if(!ctype_digit($anno))
                $messaggiForm .= '<li>Anno deve contenere solo numeri.</li>';
            else if($anno < 2000 || $anno > date("Y"))
                $messaggiForm .= '<li>Anno deve essere contenuto nel <span lang=\'en\'>range<span> 2000 e '.date("Y").'</li>';

            if(strlen($messaggiForm) == 0) {
                if($conn->openDB()) {
                    $moto = new DirtBike($id,$marca,$modello,(int)$cilindrata,(int)$anno);

                    if($conn->updateDirtBike($moto)) {
                        $messaggiForm = '<li>Informazioni sulla moto aggiornate con successo.</li>';
                        header('Location: ./#gestioneMoto');
                    } else
                        $messaggiForm = '<li>Errore durante l\'aggiornamento delle informazioni sulla moto.</li>';

                    $conn->closeDB();
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            }
        } else {
            if ($conn->openDB()) {
                try {
                    $motos = $conn->getSpecificQueryResult(str_replace('_num_', $_GET['id'], dbAccess::QUERIES[1][0]), dbAccess::QUERIES[1][1]);

                    if ($motos !== null) {
                        $moto = $motos[0];
                        unset($motos);

                        foreach($moto as $field) {
                            $field = htmlspecialchars($field);
                        }

                        $id = $moto['numero'];
                        $marca = $moto['marca'];
                        $modello = $moto['modello'];
                        $cilindrata = $moto['cilindrata'];
                        $anno = $moto['anno'];
                    } else {
                        $messaggiForm = '<li>'.dbAccess::QUERIES[1][1].'</li>';
                    }
                } catch (Throwable $t) {
                    $messaggiForm = '<li>'.$t->getMessage().'</li>';
                }

                $conn->closeDB();
            } else
                $globalError = 'Errore di connessione, riprovare più tardi.';
        }
    } else {
        //nuova moto
        $action = 'gestioneMoto.php';
        $actionText = "INSERISCI";

        //elimina id dal form
        $page = str_replace("<id/>","",$page);

        if(isset($_POST['submit'])) {
            $marca = sanitizeInputString($_POST['marca']);

            switch(checkInputValidity($marca,'/^[A-Za-zàèùìòé\s]{2,}$/')) {
                case 1: $messaggiForm .= '<li>Marca non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Marca non può contenere numeri o caratteri speciali</li>'; break;
                default: break;
            }

            $modello = sanitizeInputString($_POST['modello']);

            switch(checkInputValidity($modello,'/^[\wàèùìòé\s]{2,}$/')) {
                case 1: $messaggiForm .= '<li>Modello non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Modello non può caratteri speciali</li>'; break;
                default: break;
            }

            $cilindrata = sanitizeInputString($_POST['cilindrata']);

            switch(checkInputValidity($cilindrata,null)) {
                case 1: $messaggiForm .= '<li>Cilindrata non presente.</li>'; break;
                default: break;
            }

            if(!ctype_digit($cilindrata))
                $messaggiForm .= "<li>Cilindrata deve contenere solo numeri.</li>";

            $anno = sanitizeInputString($_POST['anno']);

            switch(checkInputValidity($anno,null)) {
                case 1: $messaggiForm .= '<li>Anno non presente.</li>'; break;
                default: break;
            }

            if(!ctype_digit($anno))
                $messaggiForm .= '<li>Anno deve contenere solo numeri.</li>';
            else if($anno < 2000 || $anno > date("Y"))
                $messaggiForm .= '<li>Anno deve essere contenuto nel <span lang=\'en\'>range<span> 2000 e '.date("Y").'</li>';

            if(strlen($messaggiForm) == 0) {
                if($conn->openDB()) {
                    $moto = new DirtBike(-1,$marca,$modello,(int)$cilindrata,(int)$anno);
                    $newId = $conn->createDirtBike($moto);

                    if($newId > -1) {
                        $messaggiForm = '<li>Nuova moto inserita con successo.</li>';
                        header('Location: ./#gestioneMoto');
                    } else
                        $messaggiForm = '<li>Errore durante l\'inserimento della nuova moto.</li>';

                    $conn->closeDB();
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            }
        } else {
            //default values (empty dirtbike)
            $anno = date("Y");
        }
    }

    if(strlen($globalError) > 0)
        $globalError = "<p class='error'>$globalError</p>";

    if(strlen($messaggiForm) > 0)
        $messaggiForm = "<ul>$messaggiForm</ul>";

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);
    $page = str_replace('<globalError/>', $globalError, $page);

    $page = str_replace('_id_',$id,$page);
    $page = str_replace('_marca_',$marca,$page);
    $page = str_replace('_modello_',$modello,$page);

    if($cilindrata != null)
        $page = str_replace("value=\"$cilindrata\"","value=\"$cilindrata\" selected",$page);

    $page = str_replace('_anno_',$anno,$page);
    $page = str_replace('_action_',$action,$page);
    $page = str_replace('_azione_',$actionText,$page);
    $page = str_replace('_maxYear_',date("Y"),$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>