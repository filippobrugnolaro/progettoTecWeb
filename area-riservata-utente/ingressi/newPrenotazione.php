<?php
    require_once('../../utils/db.php');
    require_once('../../utils/reservation.php');
    require_once('../../utils/utils.php');
    require_once('../../utils/entry.php');

    use DB\dbAccess;
    use PRENOTAZIONE\Reservation;

    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login/');


    $page = file_get_contents('ingressi/');

    $conn = new dbAccess();

    $globalError = "";
    $messaggiForm = "";

    $action = "";
    $actionText = "";

    //nuovo ingresso
    $action = 'newPrenotazione.php';
    $actionText = 'PRENOTA';

    //rende data non readonly
    //$page = str_replace('_readonly_','',$page);

    if(isset($_POST['submit'])) {
        $date = $_POST['data'];

        $date = sanitizeInputString($date);

        switch(checkInputValidity($date,'/^\d{4}-\d{2}-\d{2}$/')) {
            case 1: $messaggiForm .= '<li>Data non presente.</li>'; break;
            case 2: $messaggiForm .= '<li>Formato data non corretto</li>'; break;
            default: break;
        }

        $moto = $_POST['moto'];
        $cc = $_POST['cilindrata'];

        $abbigliamento = $_POST['vestiario'];
        $taglia = $_POST['taglia'];

        if(strlen($messaggiForm) == 0) {
            if($conn->openDB()) {
                $res = new Reservation($date, $moto, $cc, $abbigliamento, $taglia);

                $newId = $conn->createReservation($res);

                if($newId > -1) {
                    $messaggiForm = 'Ti abbiamo riservato un posto in pista.';
                    header("Location: ./#gestioneIngressi");
                } else
                    $messaggiForm = 'Errore durante la prenotazione.';

                $conn->closeDB();
            } else {
                $globalError = 'Errore di connessione, riprovare più tardi.';
            }
        } else {
            $messaggiForm = '<ul>'.$messaggiForm.'</ul>';
        }
    }

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);
    $page = str_replace('<globalError/>', $globalError, $page);

    // $page = str_replace('_data_',$date,$page);
    // $page = str_replace('_posti_',$posti,$page);

    $page = str_replace('_action_',$action,$page);
    $page = str_replace('_azione_',$actionText,$page);
    // $page = str_replace('_today_',date("Y-m-d"),$page);

    echo $page;
?>