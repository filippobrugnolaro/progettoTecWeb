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

    $tableMessaggi = '';

    if ($conn->openDB()) {
        //get rent infos

        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[18]);

            if($records !== null) {
                $tableMessaggi = "<div class=\"tableContainer\">
                                    <table title=\"tabella contenente i messaggi inviati dall'utente\">
                                        <caption>Messaggi inviati dagli utenti</caption>
                                        <thead>
                                            <tr>
                                                <th scope='col'>Nominativo</th>
                                                <th scope='col'>Data</th>
                                                <th scope='col'>Oggetto</th>
                                                <th scope='col'>Dettagli</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <messaggi/>
                                        </tbody>
                                    </table>
                                </div>";

                foreach($records as $record) {
                    $messaggi .= '<tr>';
                    $messaggi .= '<th data-title=\'nominativo\' scope=\'row\'>'.$record['nominativo'].'</th>';
                    $messaggi .= '<td data-title=\'data\'><time datetime=\''.$record['data'].'\'>'.date("d/m/Y",strtotime($record['data'])).'</time></td>';
                    $messaggi .= '<td data-title=\'oggetto\'>'.$record['oggetto'].'</td>';
                    $messaggi .= '<td data-title=\'dettagli\'><a href=\'dettaglioMessaggio.php?id='.$record['id'].'\' aria-label=\'dettaglio messaggio\'><i class=\'fa-solid fa-magnifying-glass\'></i></a></td>';
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

    if(strlen($globalError) > 0)
        $globalError = '<p class=\'error\'>'.$globalError.'</p>';

    if(strlen($errorMessaggi) > 0)
        $errorMessaggi = '<p class=\'error\'>'.$errorMessaggi.'</p>';

    $page = str_replace('_tabellaMessaggi_', $tableMessaggi, $page);
    $page = str_replace('<erroreMessaggi/>', $errorMessaggi, $page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<messaggi/>',$messaggi,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>
