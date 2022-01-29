<?php
    require_once('../utils/db.php');

    use DB\dbAccess;

    $page = file_get_contents('corsi.html');

    $recordsBody = "";
    $globalError = "";
    $errorDetails = "";

    $conn = new dbAccess();

    if($conn->openDB()) {
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[16]);

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<article>';
                    $recordsBody .= '<h2>Corso <span aria-hidden=\'true\'>#</span>'.$record['id'].'</h2>';

                    $recordsBody .= '<p>'.$record['descrizione'].'</p>';

                    $recordsBody .= '<ul>';
                    $recordsBody .= '<li>Data: <time datetime=\''.$record['data'].'\'>'.date('d/m/Y',strtotime($record['data'])).'</time></li>';
                    $recordsBody .= '<li>Istruttore: '.$record['istruttore'].'</li>';
                    $recordsBody .= '<li>Tracciato: <span aria-hidden=\'true\'>#</span>'.$record['pista'].'</li>';
                    $recordsBody .= '<li>Posti totali: '.$record['posti'].'</li>';
                    $recordsBody .= '<li>Posti disponibili: '.(($record['posti'] - $record['occupati']) >0 ? ($record['posti'] - $record['occupati']) : '0').'</li>';
                    $recordsBody .= '</ul>';

                    $recordsBody .= '</article>';
                }
            } else {
                $errorDetails = 'Nessun corso disponibile al momento.';
            }

        } catch (Throwable $t) {
            $errorDetails = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($globalError) > 0)
        $globalError = "<p>$globalError</p>";

    if(strlen($errorDetails) > 0)
        $errorDetails = "<p>$errorDetails</p>";

    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreCorsi/>',$errorDetails,$page);
    $page = str_replace('<dettaglioCorsi/>',$recordsBody,$page);

    echo $page;
?>