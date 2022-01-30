<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');

    if(!isset($_GET['date']))
        header('Location: ./');

    $date = $_GET['date'];

    $page = file_get_contents('dettagliNoleggio.html');
    $globalError = '';
    $errorDetails = '';
    $recordsBody = '';

    $conn = new dbAccess();

    if($conn->openDB()) {
        try {
            $records = $conn->getSpecificQueryResult(str_replace('_data_',$date,dbAccess::QUERIES[3][0]),dbAccess::QUERIES[3][1]);

            if($records !== null) {
                foreach($records as $record) {
                    $utente = $record['cognome'].' '.$record['nome'];
                    $moto = '<span aria-hidden=\'true\'>#</span>'.$record['numero'].' - '.$record['marca'].' '.$record['modello'].' '.$record['anno'];

                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th data-title=\'utente\' scope=\'row\'>'.$utente.'</th>';
                    $recordsBody .= '<td data-title=\'moto\'>'.$moto.'</td>';

                    if($record['attrezzatura'])
                        $recordsBody .= '<td data-title=\'attrezzatura\'>Da noleggiare</td>';
                    else
                        $recordsBody .= '<td data-title=\'attrezzatura\'>Propria</td>';

                    $recordsBody .= '</tr>';
                }
            } else {
                $errorDetails = 'Non ci sono ancora noleggi prenotati per le prossime date di apertura.';
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

    $page = str_replace('_data_',"<time datetime=\"".$date."\">".date('d/m/Y',strtotime($date))."</time>",$page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreDettagli/>',$errorDetails,$page);
    $page = str_replace('<dettaglioNoleggi/>',$recordsBody,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>