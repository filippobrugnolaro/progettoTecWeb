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
        header('Location: ../../login.php');


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

            $img = '<img src=\''.$path.'\' alt=\'immagine del circuito\'/>';
            $img .= '<caption>Immagine attuale del circuito</caption>';
            $img .= '<br>';
        }

        $page = str_replace('<id/>',$idinput,$page);

        if (isset($_POST['submit'])) {
            $lunghezza = sanitizeInputString($_POST['lunghezza']);

            switch(checkInputValidity($lunghezza,null)) {
                case 1: $messaggiForm .= '<li>Lunghezza non presente.</li>'; break;
                default: break;
            }

            if(!ctype_digit($lunghezza))
                $messaggiForm .= "<li>Lunghezza deve contenere solo numeri.</li>";

            $descrizione = sanitizeInputString($_POST['descrizione']);

            switch(checkInputValidity($descrizione,null)) {
                case 1: $messaggiForm .= '<li>Descrizione non presente.</li>'; break;
                default: break;
            }

            $terreno = sanitizeInputString($_POST['terreno']);

            switch(checkInputValidity($terreno,null)) {
                case 1: $messaggiForm .= '<li>Terreno non presente.</li>'; break;
                default: break;
            }

            $apertura = sanitizeInputString($_POST['apertura']);

            switch(checkInputValidity($apertura,'/^d{2}:d{2}\$/')) {
                case 1: $messaggiForm .= '<li>Orario apertura non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>orario apertura deve essere del tipo HH:MM</li>"; break;
                default: break;
            }

            $apertura = substr($apertura,0,5);

            if($apertura < "08:00" || $apertura > "14:00")
                $messaggiForm .= '<li>Orario apertura deve essere compreso tra le 08:00 e le 14:00.</li>';

            $chiusura = sanitizeInputString($_POST['chiusura']);

            switch(checkInputValidity($chiusura,'/^d{2}:d{2}\$/')) {
                case 1: $messaggiForm .= '<li>Orario chiusura non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>orario chiusura deve essere del tipo HH:MM</li>"; break;
                default: break;
            }

            $chiusura = substr($chiusura,0,5);

            if($chiusura < "14:00" || $chiusura > "20:00")
                $messaggiForm .= '<li>Orario chiusura deve essere compreso tra le 14:00 e le 20:00.</li>';

            if($messaggiForm == '') {
                if($conn->openDB()) {
                    $imgFile = glob("../../images/tracks/$id.*");

                    if($imgFile !== false && !empty($imgFile))
                        $path = $id.'.'.pathinfo($imgFile[0],PATHINFO_EXTENSION);
                    else
                        $path = "";

                    $track = new Track((int) $id,(int) $lunghezza,$descrizione,$terreno,$apertura,$chiusura);
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
                                $errors .= '<li>File non accettato</li>';

                            if(!in_array($fileType,$allowedExt))
                                $errors .= '<li>Formato immagine non accettato</li>';

                            if($_FILES['img']['size'] > 5000000)
                                $errors .= '<li>File troppo grande.</li>';

                            if($errors == '') {
                                $fileName = $finalDir.$id.'.'.$fileType;

                                foreach(glob("../../images/tracks/$id.*") as $file)
                                    unlink($file);

                                if(move_uploaded_file($_FILES['img']['tmp_name'],$fileName))
                                    $messaggiForm .= '<li>File caricato con successo</li>';
                                else
                                    $messaggiForm .= '<li>Errore durante il carimento del file</li>';
                            } else
                                $messaggiForm .= $errors;

                            $messaggiForm = '<ul>'.$messaggiForm.'</ul>';

                            $track->setImgPath($id.'.'.$fileType);

                            $conn->updateTrack($track);
                        }
                        $conn->closeDB();
                        header("Location: gestioneTracciato.php?id=$id"); //MOMENTANEA -> DA VALUTARE (non va troppo bene, utente non capisce se file si è caricato o meno)
                    } else {
                        $messaggiForm = 'Errore durante l\'inserimento del tracciato.';
                        $conn->closeDB();
                    }
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            } else {
                $messaggiForm = '<ul>'.$messaggiForm.'</ul>';
            }
        } else {
            if ($conn->openDB()) {
                try {
                    $track = $conn->getSpecificQueryResult(str_replace('_id_', $_GET['id'], dbAccess::QUERIES[5][0]), dbAccess::QUERIES[5][1]);

                    if ($track !== null) {
                        $track = $track[0];

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
                        $messaggiForm = dbAccess::QUERIES[1][1];
                    }
                } catch (Throwable $t) {
                    $messaggiForm = $t->getMessage();
                }

                $conn->closeDB();
            } else
                $globalError = 'Errore di connessione, riprovare più tardi.';
        }
    } else {
        //nuova moto
        $action = 'gestioneTracciato.php';
        $actionText = "INSERISCI";

        //elimina id dal form
        $page = str_replace("<id/>","",$page);

        if(isset($_POST['submit'])) {
            $lunghezza = sanitizeInputString($_POST['lunghezza']);

            switch(checkInputValidity($lunghezza,null)) {
                case 1: $messaggiForm .= '<li>Lunghezza non presente.</li>'; break;
                default: break;
            }

            if(!ctype_digit($lunghezza))
                $messaggiForm .= "<li>Lunghezza deve contenere solo numeri.</li>";

            $descrizione = sanitizeInputString($_POST['descrizione']);

            switch(checkInputValidity($descrizione,null)) {
                case 1: $messaggiForm .= '<li>Descrizione non presente.</li>'; break;
                default: break;
            }

            $terreno = sanitizeInputString($_POST['terreno']);

            switch(checkInputValidity($terreno,null)) {
                case 1: $messaggiForm .= '<li>Terreno non presente.</li>'; break;
                default: break;
            }

            $apertura = sanitizeInputString($_POST['apertura']);

            switch(checkInputValidity($apertura,'/^d{2}:d{2}\$/')) {
                case 1: $messaggiForm .= '<li>Orario apertura non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>orario apertura deve essere del tipo HH:MM</li>"; break;
                default: break;
            }

            $apertura = substr($apertura,0,5);

            if($apertura < "08:00" || $apertura > "14:00")
                $messaggiForm .= '<li>Orario apertura deve essere compreso tra le 08:00 e le 14:00.</li>';

            $chiusura = sanitizeInputString($_POST['chiusura']);

            switch(checkInputValidity($chiusura,'/^d{2}:d{2}\$/')) {
                case 1: $messaggiForm .= '<li>Orario chiusura non presente.</li>'; break;
                case 2: $messaggiForm .= "<li>orario chiusura deve essere del tipo HH:MM</li>"; break;
                default: break;
            }

            $chiusura = substr($chiusura,0,5);

            if($chiusura < "14:00" || $chiusura > "20:00")
            $messaggiForm .= '<li>Orario chiusura deve essere compreso tra le 14:00 e le 20:00.</li>';

            if($messaggiForm == '') {
                if($conn->openDB()) {
                    $track = new Track(-1,(int) $lunghezza,$descrizione,$terreno,$apertura,$chiusura);

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
                            $errors .= '<li>File non accettato</li>';

                        if(!in_array($fileType,$allowedExt))
                            $errors .= '<li>Formato immagine non accettato</li>';

                        if($_FILES['img']['size'] > 5000000)
                            $errors .= '<li>File troppo grande.</li>';

                        if($errors == '') {
                            $fileName = $finalDir.$newId.'.'.$fileType;

                            if(move_uploaded_file($_FILES['img']['tmp_name'],$fileName))
                                $messaggiForm .= '<li>File caricato con successo</li>';
                            else
                                $messaggiForm .= '<li>Errore durante il carimento del file</li>';
                        } else
                            $messaggiForm .= $errors;

                        $messaggiForm = '<ul>'.$messaggiForm.'</ul>';

                        $track->setNewId($newId);
                        $track->setImgPath($newId.'.'.$fileType);

                        $conn->updateTrack($track);
                    }
                        $conn->closeDB();

                        header("Location: gestioneTracciato.php?id=$newId"); //MOMENTANEA -> DA VALUTARE (non va troppo bene, utente non capisce se file si è caricato o meno)
                    } else {
                        $messaggiForm = 'Errore durante l\'inserimento del tracciato.';
                        $conn->closeDB();
                    }
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            } else {
                $messaggiForm = '<ul>'.$messaggiForm.'</ul>';
            }
        } else {
            //default values (empty id)
            $lunghezza = 1000;
        }

    }

    $page = str_replace('img_path', "../".$_SESSION['user']->getImgPath(), $page);
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

    echo $page;
?>