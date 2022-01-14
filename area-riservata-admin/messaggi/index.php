<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('messaggi.html');

    $conn = new dbAccess();

    $globalError = '';
    $errorMessaggi = '';
    $messaggi = '';

    if ($conn->openDB()) {
        //get rent infos

        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[18]);

            if($records !== null) {
                foreach($records as $record) {
                    $messaggi .= '<tr>';
                    $messaggi .= '<td>'.date("d/m/Y",strtotime($record['data'])).'</td>';
                    $messaggi .= '<td scope=\'row\'>'.$record['nominativo'].'</td>';
                    $messaggi .= '<td>'.$record['oggetto'].'</td>';
                    $messaggi .= '<td><a href=\'dettaglioMessaggio.php?id='.$record['id'].'\' aria-label=\'dettaglio messaggio\'><i class=\'fa-solid fa-magnifying-glass\'></i></a></td>';
                    $messaggi .= '</tr>';
                }
            } else {
                $errorMessaggi = 'Non ci sono messaggi inviati dagli utenti.';
            }

        } catch (Throwable $t) {
            $errorMessaggi = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('<erroreMessaggi/>', $errorMessaggi, $page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<messaggi/>',$messaggi,$page);

    echo $page;
?>
