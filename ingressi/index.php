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
            $records = $conn->getQueryResult(dbAccess::QUERIES[24]);

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th data-title="data" scope=\'row\'><time datetime=\''.$record['data'].'\'>'.date('d/m/Y',strtotime($record['data'])).'</time></th>';
                    $recordsBody .= '<td data-title="posti disponibili">'.$record['posti'].'</td>';
                    $recordsBody .= '<td data-title="posti occupati">'.(($record['posti'] - $record['occupati']) >0 ? ($record['posti'] - $record['occupati']) : '0').'</td>';
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

    if(strlen($globalError) > 0)
        $globalError = "<p class=\"error\">$globalError</p>";

    if(strlen($errorDetails) > 0)
        $errorDetails = "<p class=\"error\">$errorDetails</p>";

    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreIngressi/>',$errorDetails,$page);
    $page = str_replace('<dettaglioIngressi/>',$recordsBody,$page);

    echo $page;
?>