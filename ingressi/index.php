<?php
    require_once('../utils/db.php');

    use DB\dbAccess;

    $page = file_get_contents('ingressi.html');

    $conn = new dbAccess();

    $recordsBody = "";
    $globalError = "";
    $errorDetails = "";

    if($conn->openDB()) {
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[17]);

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th scope=\'row\'>'.date('d/m/Y',strtotime($record['data'])).'</th>';
                    $recordsBody .= '<td>'.$record['posti'].'</td>';
                    $recordsBody .= '<td>'.($record['posti'] - $record['occupati']).'</td>';
                    $recordsBody .= '</tr>';
                }
            } else {
                $errorDetails = 'Nessun ingresso prenotabile disponibile.';
            }

        } catch (Throwable $t) {
            $errorDetails = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreIngressi/>',$errorDetails,$page);
    $page = str_replace('<dettaglioIngressi/>',$recordsBody,$page);

    echo $page;
?>