<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');
    require_once('../../utils/utils.php');
    require_once('../../utils/entry.php');

    use DB\dbAccess;
    use INGRESSO\Entry;

    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('gestioneIngresso.html');

    $conn = new dbAccess();

    $globalError = "";
    $messaggiForm = "";
    $action = "";
    $actionText = "";

    $date = "";
    $posti = "";

    if (isset($_GET['date'])) {
        //modifica ingresso
        $action = 'gestioneIngresso.php?date=' . $_GET['date'];
        $actionText = 'AGGIORNA';

        $page = str_replace('_readonly_','readonly',$page);

        if (isset($_POST['submit'])) {
            $date = sanitizeInputString($_GET['date']);

            switch(checkInputValidity($date,'/^\d{4}-\d{2}-\d{2}$/')) {
                case 1: $messaggiForm .= '<li>Data non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Formato data non corretto.</li>'; break;
                default: break;
            }

            if(strtotime($date) < strtotime(date('Y-m-d')))
                $messaggiForm .= '<li>La data non può essere antecedente alla data odierna.</li>';

            $posti = sanitizeInputString($_POST['posti']);

            switch(checkInputValidity($posti,'/^[0-9]{2,3}$/')) {
                case 1: $messaggiForm .= '<li>Numero posti disponibili non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Numero inserito non valido.</li>'; break;
                default: break;
            }

            if($posti < 50 || $posti > 200)
                $messaggiForm .= '<li>Numero posti disponibili deve essere compreso tra 50 e 200.</li>';

            if(strlen($messaggiForm) == 0) {
                if($conn->openDB()) {
                    $entry = new Entry($date,$posti);

                    if($conn->updateEntry($entry)) {
                        $messaggiForm = '<li>Informazioni sull\'ingresso aggiornate con successo.</li>';
                        header("Location: ./#gestioneIngressi");
                    } else
                        $messaggiForm = '<li>Errore durante l\'aggiornamento delle informazioni sull\'ingresso.</li>';

                    $conn->closeDB();
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            }
        } else {
            if ($conn->openDB()) {
                try {
                    $entries = $conn->getSpecificQueryResult(str_replace('_data_', $_GET['date'], dbAccess::QUERIES[9][0]), dbAccess::QUERIES[9][1]);

                    if ($entries !== null) {
                        $entry = $entries[0];
                        unset($entries);

                        foreach($entry as $field) {
                            $field = htmlspecialchars($field);
                        }

                        $date = $_GET['date'];
                        $posti = $entry['posti'];
                    } else {
                        $messaggiForm = '<li>'.dbAccess::QUERIES[9][1].'</li>';
                    }
                } catch (Throwable $t) {
                    $messaggiForm = '<li>'.$t->getMessage().'</li>';
                }

                $conn->closeDB();
            } else
                $globalError = 'Errore di connessione, riprovare più tardi.';
        }
    } else {
        //nuovo ingresso
        $action = 'gestioneIngresso.php';
        $actionText = 'INSERISCI';

        //rende data non readonly
        $page = str_replace('_readonly_','',$page);

        if(isset($_POST['submit'])) {
            $date = sanitizeInputString($_POST['data']);

            switch(checkInputValidity($date,'/^\d{4}-\d{2}-\d{2}$/')) {
                case 1: $messaggiForm .= '<li>Data non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Formato data non corretto.</li>'; break;
                default: break;
            }

            if(strtotime($date) < strtotime(date('Y-m-d')))
                $messaggiForm .= '<li>La data non può essere antecedente alla data odierna.</li>';

            $posti = sanitizeInputString($_POST['posti']);

            switch(checkInputValidity($posti,'/^[0-9]{2,3}$/')) {
                case 1: $messaggiForm .= '<li>Numero posti disponibili non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Numero inserito non valido.</li>'; break;
                default: break;
            }

            if($posti < 50 || $posti > 200)
                $messaggiForm .= '<li>Numero posti disponibili deve essere compreso tra 50 e 200.</li>';

            if(strlen($messaggiForm) == 0) {
                if($conn->openDB()) {
                    $entry = new Entry($date,$posti);
                    $newId = $conn->createEntry($entry);

                    if($newId > -1) {
                        $messaggiForm = '<li>Nuova data d\'apertura inserita con successo.</li>';
                        header("Location: ./#gestioneIngressi");
                    } else
                        $messaggiForm = '<li>Errore durante l\'inserimento della nuova data d\'apertura.</li>';

                    $conn->closeDB();
                } else {
                    $globalError = 'Errore di connessione, riprovare più tardi.';
                }
            }
        } else {
            //valori di default
            $posti = 100;
        }
    }

    if(strlen($messaggiForm) > 0)
        $messaggiForm = "<ul>$messaggiForm</ul>";

    if(strlen($globalError) > 0)
        $globalError = "<p class='error'>$globalError</p>";

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);
    $page = str_replace('<globalError/>', $globalError, $page);

    $page = str_replace('_data_',$date,$page);
    $page = str_replace('_posti_',$posti,$page);

    $page = str_replace('_action_',$action,$page);
    $page = str_replace('_azione_',$actionText,$page);
    $page = str_replace('_today_',date("Y-m-d"),$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>