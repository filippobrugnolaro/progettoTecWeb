<?php
    require_once('../utils/db.php');

    use DB\dbAccess;

    $page = file_get_contents('noleggio.html');

    $globalError = '';
    $errorDetails = '';
    $recordsBody = '';

    $conn = new dbAccess();

    if($conn->openDB()) {
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[0]);

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<td>'.$record['marca'].'</td>';
                    $recordsBody .= '<td>'.$record['modello'].'</td>';
                    $recordsBody .= '<td>'.$record['cilindrata'].'<abbr title=\'Centimetri cubici\'>cc</abbr></td>';
                    $recordsBody .= '<td>'.$record['anno'].'</td>';
                    $recordsBody .= '</tr>';
                }
            } else {
                $errorDetails = 'Attualmente non ci sono moto noleggiabili';
            }

        } catch (Throwable $t) {
            $errorDetails = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreMotoDisp>',$errorDetails,$page);
    $page = str_replace('<dettaglioMotoDisp/>',$recordsBody,$page);

    echo $page;
?>