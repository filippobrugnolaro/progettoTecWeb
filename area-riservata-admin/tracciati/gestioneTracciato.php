<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');
    require_once('../../utils/moto.php');
    require_once('../../utils/utils.php');

    use DB\dbAccess;
    use TRACCIATO\Track;

    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('gestioneTracciato.html');

    $conn = new dbAccess();

    $globalError = "";
    $messaggiForm = "";
    $action = "";
    $actionText = "";

    $id = "";
    $lunghezza = "";
    $descrizione = "";
    $terreno = "";
    $apertura = "";
    $chiusura = "";
    $img = "";

    $requiredImg = "";

    if (isset($_GET['id'])) {
        //modifica tracciati
        $id = $_GET['id'];

        $action = 'gestioneTracciato.php?id=' . $_GET['id'];
        $actionText = "AGGIORNA";

        $idinput = '<label for=\'identificativo\'>Identificativo</label>';
        $idinput .= '<input type=\'text\' readonly name=\'identificativo\' id=\'identificativo\' value=\'_id_\'> <br>';

        $imgFile = glob("../../images/tracks/$id.*");

        if($imgFile !== false && !empty($imgFile))
            $path = $id.'.'.pathinfo($imgFile[0],PATHINFO_EXTENSION);
        else
            $path = "";

        if($path != "") {
            $path = '../../images/tracks/'.$path;

            $img = '<p id="label">Immagine attuale</p>';
            $img .= '<img src=\''.$path.'\' alt=\'immagine del circuito\'>';
        }

        $page = str_replace('<id/>',$idinput,$page);

        if (isset($_POST['submit'])) {
            $lunghezza = sanitizeInputString($_POST['lunghezza']);

            switch(checkInputValidity($lunghezza,'/^[0-9]{3,5}$/')) {
                case 1: $messaggiForm .= '<li>Lunghezza non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>Lunghezza deve contenere da 3 a 5 cifre.</li>"; break;
                default: break;
            }

            if($lunghezza < 500 || $lunghezza > 10000)
                $messaggiForm .= '<li>Lunghezza fuori dai limiti. Deve essere compresa tra 500 e 10000.</li>';

            if(strlen(trim($_POST['descrizione'])) > 0) {
                    $desc = sanitizeInputString($_POST['descrizione']);

                    switch(checkInputValidity($desc,'/^.{30,300}$/')) {
                        case 1: $messaggiForm .= '<li>Descrizione non presente.</li>'; break;
                        case 2: $messaggiForm .= '<li>La descrizione deve essere compresa tra 30 e 300 caratteri.</li>'; break;
                        default: break;
                    }
            } else {
                $messaggiForm .= '<li>Descrizione non presente.</li>';
            }

            $terreno = sanitizeInputString($_POST['terreno']);

            switch(checkInputValidity($terreno,null)) {
                case 1: $messaggiForm .= '<li>Terreno non presente.</li>'; break;
                default: break;
            }

            $apertura = sanitizeInputString($_POST['apertura']);
            $apertura = substr($apertura,0,5);

            switch(checkInputValidity($apertura,'/^\d{2}:\d{2}$/')) {
                case 1: $messaggiForm .= '<li>Orario apertura non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>orario apertura deve essere del tipo HH:MM</li>"; break;
                default: break;
            }

            if($apertura < "08:00" || $apertura > "14:00")
                $messaggiForm .= '<li>Orario apertura deve essere compreso tra le 08:00 e le 14:00.</li>';

            $chiusura = sanitizeInputString($_POST['chiusura']);
            $chiusura = substr($chiusura,0,5);

            switch(checkInputValidity($chiusura,'/^\d{2}:\d{2}$/')) {
                case 1: $messaggiForm .= '<li>Orario chiusura non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>orario chiusura deve essere del tipo HH:MM</li>"; break;
                default: break;
            }

            if($chiusura < "14:00" || $chiusura > "20:00")
                $messaggiForm .= '<li>Orario chiusura deve essere compreso tra le 14:00 e le 20:00.</li>';

            if(strlen($messaggiForm) == 0) {
                if($conn->openDB()) {
                    $imgFile = glob("../../images/tracks/$id.*");

                    if($imgFile !== false && !empty($imgFile))
                        $path = $id.'.'.pathinfo($imgFile[0],PATHINFO_EXTENSION);
                    else
                        $path = "";

                    $track = new Track((int) $id,(int) $lunghezza,$desc,$terreno,$apertura,$chiusura);
                    $track->setImgPath($path);

                    if($conn->updateTrack($track)) {
                        $messaggiForm = '<li>Tracciato aggiornato con successo.</li>';

                        //now perform file-upload
                        if($_FILES['img']['size'] != 0 && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
                            $errors = "";

                            $finalDir = '../../images/tracks/';
                            $fileType = strtolower(pathinfo($_FILES['img']['name'],PATHINFO_EXTENSION));

                            $allowedExt = array('jpg','png','jpeg','gif');

                            if(getimagesize($_FILES['img']['tmp_name']) === false)
                                $errors .= '<li>File non accettato.</li>';

                            if(!in_array($fileType,$allowedExt))
                                $errors .= '<li>Formato immagine non accettato.</li>';

                            if($_FILES['img']['size'] > 5000000)
                                $errors .= '<li>File troppo grande.</li>';

                            if(strlen($errors) == 0) {
                                $fileName = $finalDir.$id.'.'.$fileType;

                                foreach(glob("../../images/tracks/$id.*") as $file)
                                unlink($file);

                                if(move_uploaded_file($_FILES['img']['tmp_name'],$fileName)) {
                                    $messaggiForm .= '<li>File caricato con successo.</li>';
                                } else
                                    $messaggiForm .= '<li>Errore durante il carimento del file.</li>';
                            } else
                                $messaggiForm .= $errors;

                            $messaggiForm = '<ul>'.$messaggiForm.'</ul>';

                            $track->setImgPath($id.'.'.$fileType);
                            $conn->updateTrack($track);
                        }

                        $conn->closeDB();
                        header('Location: ./#gestioneTracciato'); //utente non capisce se file si è caricato o meno)
                    } else {
                        $messaggiForm = '<li>Errore durante l\'inserimento del tracciato.</li>';
                        $conn->closeDB();
                    }
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            }
        } else {
            if ($conn->openDB()) {
                try {
                    $tracks = $conn->getSpecificQueryResult(str_replace('_id_', $_GET['id'], dbAccess::QUERIES[5][0]), dbAccess::QUERIES[5][1]);

                    if ($tracks !== null) {
                        $track = $tracks[0];
                        unset($tracks);

                        foreach($track as $field) {
                            $field = htmlspecialchars($field);
                        }

                        $id = $track['id'];
                        $lunghezza = $track['lunghezza'];
                        $descrizione = $track['descrizione'];
                        $terreno = $track['terreno'];
                        $apertura = $track['apertura'];
                        $chiusura = $track['chiusura'];
                    } else {
                        $messaggiForm = '<li>'.dbAccess::QUERIES[5][1].'</li>';
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
        $action = 'gestioneTracciato.php';
        $actionText = "INSERISCI";

        $requiredImg = 'required aria-required="true"';

        //elimina id dal form
        $page = str_replace("<id/>","",$page);

        if(isset($_POST['submit'])) {
            $lunghezza = sanitizeInputString($_POST['lunghezza']);

            switch(checkInputValidity($lunghezza,'/^[0-9]{3,5}$/')) {
                case 1: $messaggiForm .= '<li>Lunghezza non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>Lunghezza deve contenere da 3 a 5 cifre.</li>"; break;
                default: break;
            }

            if($lunghezza < 500 || $lunghezza > 10000)
                $messaggiForm .= '<li>Lunghezza fuori dai limiti. Deve essere compresa tra 500 e 10000.</li>';

            if(strlen(trim($_POST['descrizione'])) > 0) {
                    $desc = sanitizeInputString($_POST['descrizione']);

                    switch(checkInputValidity($desc,'/^.{30,300}$/')) {
                        case 1: $messaggiForm .= '<li>Descrizione non presente.</li>'; break;
                        case 2: $messaggiForm .= '<li>La descrizione deve essere compresa tra 30 e 300 caratteri.</li>'; break;
                        default: break;
                    }
            } else {
                $messaggiForm .= '<li>Descrizione non presente.</li>';
            }

            $terreno = sanitizeInputString($_POST['terreno']);

            switch(checkInputValidity($terreno,null)) {
                case 1: $messaggiForm .= '<li>Terreno non presente.</li>'; break;
                default: break;
            }

            $apertura = sanitizeInputString($_POST['apertura']);
            $apertura = substr($apertura,0,5);

            switch(checkInputValidity($apertura,'/^\d{2}:\d{2}$/')) {
                case 1: $messaggiForm .= '<li>Orario apertura non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>orario apertura deve essere del tipo HH:MM</li>"; break;
                default: break;
            }

            if($apertura < "08:00" || $apertura > "14:00")
                $messaggiForm .= '<li>Orario apertura deve essere compreso tra le 08:00 e le 14:00.</li>';

            $chiusura = sanitizeInputString($_POST['chiusura']);
            $chiusura = substr($chiusura,0,5);

            switch(checkInputValidity($chiusura,'/^\d{2}:\d{2}$/')) {
                case 1: $messaggiForm .= '<li>Orario chiusura non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>orario chiusura deve essere del tipo HH:MM</li>"; break;
                default: break;
            }

            if($chiusura < "14:00" || $chiusura > "20:00")
                $messaggiForm .= '<li>Orario chiusura deve essere compreso tra le 14:00 e le 20:00.</li>';

            if($_FILES['img']['error'] != UPLOAD_ERR_OK)
                $messaggiForm .= '<li>Errore durante il carimento del file.</li>';

            if(strlen($messaggiForm) == 0) {
                if($conn->openDB()) {
                    $track = new Track(-1,(int) $lunghezza,$desc,$terreno,$apertura,$chiusura);

                    $newId = $conn->createTrack($track);

                    if($newId > -1) {
                        $messaggiForm = '<li>Nuovo tracciato inserito con successo.</li>';

                        //now perform file-upload
                        if($_FILES['img']['size'] != 0 && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
                            $errors = "";

                            $finalDir = '../../images/tracks/';
                            $fileType = strtolower(pathinfo($_FILES['img']['name'],PATHINFO_EXTENSION));

                            $allowedExt = array('jpg','png','jpeg','gif');

                            if(getimagesize($_FILES['img']['tmp_name']) === false)
                                $errors .= '<li>File non accettato.</li>';

                            if(!in_array($fileType,$allowedExt))
                                $errors .= '<li>Formato immagine non accettato.</li>';

                            if($_FILES['img']['size'] > 5000000)
                                $errors .= '<li>File troppo grande.</li>';

                            if(strlen($errors) == 0) {
                                $fileName = $finalDir.$newId.'.'.$fileType;

                                if(move_uploaded_file($_FILES['img']['tmp_name'],$fileName))
                                    $messaggiForm .= '<li>File caricato con successo.</li>';
                                else
                                    $messaggiForm .= '<li>Errore durante il carimento del file.</li>';
                            } else
                                $messaggiForm .= $errors;

                            $track->setNewId($newId);
                            $track->setImgPath($newId.'.'.$fileType);
                            $conn->updateTrack($track);

                            $conn->closeDB();
                            header('Location: ./#gestioneTracciato');
                        }

                    } else {
                        $messaggiForm = '<li>Errore durante l\'inserimento del tracciato.</li>';
                        $conn->closeDB();
                    }
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            }
        } else {
            //default values (empty id)
            $lunghezza = 1000;
        }
    }

    if(strlen($globalError) > 0)
        $globalError = "<p class='error'>$globalError</p>";

    if(strlen($messaggiForm) > 0)
        $messaggiForm = "<ul>$messaggiForm</ul>";

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);
    $page = str_replace('<globalError/>', $globalError, $page);

    $page = str_replace('_azione_',$actionText,$page);
    $page = str_replace('_action_',$action,$page);

    $page = str_replace('_id_',$id,$page);
    $page = str_replace('<oldImg/>',$img,$page);
    $page = str_replace('_lunghezza_',$lunghezza,$page);
    $page = str_replace('_descrizione_',$descrizione,$page);
    $page = str_replace("value=\"$terreno\"","value=\"$terreno\" selected",$page);
    $page = str_replace('_apertura_',$apertura,$page);
    $page = str_replace('_chiusura_',$chiusura,$page);

    $page = str_replace('_requiredImg_',$requiredImg,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>