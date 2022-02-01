<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');
    require_once('../../utils/utils.php');
    require_once('../../utils/lesson.php');

    use DB\dbAccess;
    use LEZIONE\Lesson;

    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('gestioneCorso.html');

    $conn = new dbAccess();

    $globalError = "";
    $messaggiForm = "";
    $action = "";
    $actionText = "";

    $date = ""; //serie di option
    $data = ""; //data scelta
    $posti = "";
    $desc = "";
    $istruttore = "";
    $tracciati = ""; //serie di option
    $tracciato = ""; //tracciato scelto

    if (isset($_GET['id'])) {
        //modifica corso
        $action = 'gestioneCorso.php?id=' . $_GET['id'];
        $actionText = 'AGGIORNA';

        $idinput = '<label for=\'identificativo\'>Identificativo corso</label>';
        $idinput .= '<input type=\'text\' readonly name=\'identificativo\' id=\'identificativo\' value=\'_id_\'> <br>';

        $id = $_GET['id'];

        $page = str_replace('<id/>',$idinput,$page);

        if (isset($_POST['submit'])) {
            $data = sanitizeInputString($_POST['data']);

            switch(checkInputValidity($data,'/^\d{4}-\d{2}-\d{2}$/')) {
                case 1: $messaggiForm .= '<li>Data non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Formato data non corretto</li>'; break;
                default: break;
            }

            $posti = sanitizeInputString($_POST['posti']);

            switch(checkInputValidity($posti,'/^[0-9]{1,2}$/')) {
                case 1: $messaggiForm .= '<li>Numero posti disponibili non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Numero deve avere da 2 a 3 cifre.</li>'; break;
                default: break;
            }

            if($posti < 2 || $posti > 15)
                $messaggiForm .= '<li>Numero posti disponibili deve essere compreso tra 2 e 15.</li>';


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

            $istruttore = sanitizeInputString($_POST['istruttore']);

            switch(checkInputValidity($istruttore,'/^[A-Za-zàèùìòé\'\s]{2,}$/')) {
                case 1: $messaggiForm .= '<li>Istruttore non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Istruttore non può contenere numeri e caratteri speciali.</li>'; break;
                default: break;
            }

            $tracciato = sanitizeInputString($_POST['tracciato']);

            switch(checkInputValidity($tracciato,null)) {
                case 1: $messaggiForm .= '<li>Tracciato non presente.</li>'; break;
                default: break;
            }

            if(!ctype_digit($tracciato))
                $messaggiForm .= '<li>Il tracciato deve essere un numero.</li>';


            if(strlen($messaggiForm) == 0) {
                if($conn->openDB()) {
                    $lesson = new Lesson($id,$data,$desc,$istruttore,$tracciato,$posti);

                    if($conn->updateLesson($lesson)) {
                        $messaggiForm = '<li class=\'success\'>Informazioni sul corso aggiornate con successo.</li>';
                        header("Location: ./#gestioneCorsi");
                    } else
                        $messaggiForm = '<li class=\'error\'>Errore durante l\'aggiornamento delle informazioni sul corso. Controllare che non vi siano conflitti tra i corsi.</li>';

                    $conn->closeDB();
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            } else {
                $messaggiForm = '<ul>'.$messaggiForm.'</ul>';
            }

        } else {
            if ($conn->openDB()) {
                try {
                    $lessons = $conn->getSpecificQueryResult(str_replace('_lezione_', $_GET['id'], dbAccess::QUERIES[13][0]), dbAccess::QUERIES[13][1]);

                    if ($lessons !== null) {
                        $lesson = $lessons[0];
                        unset($lessons);

                        foreach($lesson as $field) {
                            $field = htmlspecialchars($field);
                        }

                        $posti = $lesson['posti'];
                        $data = $lesson['data'];
                        $desc = $lesson['descrizione'];
                        $istruttore = $lesson['istruttore'];
                        $tracciato = $lesson['pista'];
                    } else {
                        $messaggiForm = dbAccess::QUERIES[13][1];
                    }
                } catch (Throwable $t) {
                    $messaggiForm = $t->getMessage();
                }

                $conn->closeDB();
            } else
                $globalError = 'Errore di connessione, riprovare più tardi.';
        }
    } else {
        //nuovo corso
        $action = 'gestioneCorso.php';
        $actionText = 'INSERISCI';

        //elimina id dal form
        $page = str_replace("<id/>","",$page);

        if(isset($_POST['submit'])) {
            $data = sanitizeInputString($_POST['data']);

            switch(checkInputValidity($data,'/^\d{4}-\d{2}-\d{2}$/')) {
                case 1: $messaggiForm .= '<li>Data non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Formato data non corretto</li>'; break;
                default: break;
            }

            $posti = sanitizeInputString($_POST['posti']);

            switch(checkInputValidity($posti,'/^[0-9]{1,2}$/')) {
                case 1: $messaggiForm .= '<li>Numero posti disponibili non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Numero deve avere da 2 a 3 cifre.</li>'; break;
                default: break;
            }

            if($posti < 2 || $posti > 15)
                $messaggiForm .= '<li>Numero posti disponibili deve essere compreso tra 2 e 15.</li>';

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

            $istruttore = sanitizeInputString($_POST['istruttore']);

            switch(checkInputValidity($istruttore,'/^[A-Za-zàèùìòé\'\s]{2,}$/')) {
                case 1: $messaggiForm .= '<li>Istruttore non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Istruttore non può contenere numeri e caratteri speciali.</li>'; break;
                default: break;
            }

            $tracciato = sanitizeInputString($_POST['tracciato']);

            switch(checkInputValidity($tracciato,null)) {
                case 1: $messaggiForm .= '<li>Tracciato non presente.</li>'; break;
                default: break;
            }

            if(!ctype_digit($tracciato))
                $messaggiForm .= '<li>Il tracciato deve essere un numero.</li>';

            if(strlen($messaggiForm) == 0) {
                if($conn->openDB()) {
                    $lesson = new Lesson(-1,$data,$desc,$istruttore,$tracciato,$posti);

                    $newId = $conn->createLesson($lesson);

                    if($newId > -1) {
                        $messaggiForm = '<li class=\'success\'>Nuovo corso inserito con successo.</li>';
                        header("Location: ./#gestioneCorsi");
                    } else if ($newId == -2) $messaggiForm = '<li class=\'error\'>Esiste già un corso in questa data e in questa pista.</li>';
                    else $messaggiForm = '<li class=\'error\'>Errore durante l\'inserimento del nuovo corso.</li>';

                    $conn->closeDB();
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            } else {
                $messaggiForm = '<ul>'.$messaggiForm.'</ul>';
            }
        }
    }

    if($conn->openDB()) {
        //recupero date disponibili
        try {
            $eventi = $conn->getQueryResult(dbAccess::QUERIES[7]);

            if($eventi != null) {
                if($data != "") {
                    foreach($eventi as $evento) {
                        if($evento['data'] == $data)
                            $date .= '<option value=\''.$evento['data'].'\' selected>'.date('d/m/Y',strtotime($evento['data'])).'</option>';
                        else
                            $date .= '<option value=\''.$evento['data'].'\'>'.date('d/m/Y',strtotime($evento['data'])).'</option>';
                    }
                } else {
                    foreach($eventi as $evento)
                        $date .= '<option value=\''.$evento['data'].'\'>'.date('d/m/Y',strtotime($evento['data'])).'</option>';
                }
            }
        } catch (Throwable $t) {
            $messaggiForm .= "<li class=\"error\">".$t->getMessage()."</li>";
        }

        //recupero piste disponibili
        try {
            $tracks = $conn->getQueryResult(dbAccess::QUERIES[4]);

            if($tracks != null) {
                if($tracciato != "") {
                    foreach($tracks as $track) {
                        if($track['id'] == $tracciato)
                            $tracciati .= '<option value=\''.$track['id'].'\' selected>#'.$track['id'].'</option>';
                        else
                            $tracciati .= '<option value=\''.$track['id'].'\'>#'.$track['id'].'</option>';
                    }
                } else {
                    foreach($tracks as $track)
                        $tracciati .= '<option value=\''.$track['id'].'\'>#'.$track['id'].'</option>';
                }
            }
        } catch (Throwable $t) {
            $messaggiForm .= "<li class=\"error\">".$t->getMessage()."</li>";
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($messaggiForm) > 0)
        $messaggiForm = "<ul>$messaggiForm</ul>";

    if(strlen($globalError) > 0)
        $globalError = "<p class=\"error\">$globalError</p>";

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);
    $page = str_replace('<globalError/>', $globalError, $page);

    $page = str_replace('_dateDisponibili_',$date,$page);
    $page = str_replace('_posti_',$posti,$page);
    $page = str_replace('_descrizione_',$desc,$page);
    $page = str_replace('_istruttore_',$istruttore,$page);
    $page = str_replace('_tracciati_',$tracciati,$page);

    $page = str_replace('_action_',$action,$page);
    $page = str_replace('_azione_',$actionText,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>