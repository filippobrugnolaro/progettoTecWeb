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
                    $f = $record['cilindrata'] == 250 || $record['cilindrata'] == 450 ? 'f' : '';

                    $recordsBody .= '<tr>';
                    $recordsBody .= '<td data-title="marca">'.$record['marca'].'</td>';
                    $recordsBody .= '<td data-title="modello">'.$record['modello'].'</td>';
                    $recordsBody .= '<td data-title="cilindrata">'.$record['cilindrata'].'<abbr title=\'Centimetri cubici\'>cc</abbr></td>';
                    $recordsBody .= '<td data-title="anno">'.$record['anno'].'</td>';
                    $recordsBody .= '<td data-title="disponibilita">'.$record['disponibili'].'</td>';
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

    if(strlen($globalError) > 0)
        $globalError = "<p class=\"error\">$globalErorr</p>";

    if(strlen($errorDetails) > 0)
        $errorDetails = "<p class=\"error\">$errorDetails</p>";

    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreMotoDisp/>',$errorDetails,$page);
    $page = str_replace('<dettaglioMotoDisp/>',$recordsBody,$page);

    echo $page;
?>