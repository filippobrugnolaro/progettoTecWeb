<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');

    if(!isset($_GET['id']))
        header('Location: ./');

    $id = $_GET['id'];

    $page = file_get_contents('dettagliCorso.html');
    $globalError = '';
    $errorDetails = '';
    $recordsBody = '';
    $table = '';

    $conn = new dbAccess();

    if($conn->openDB()) {
        try {
            $records = $conn->getSpecificQueryResult(str_replace('_lezione_',$id,dbAccess::QUERIES[12][0]),dbAccess::QUERIES[8][1]);

            if($records !== null) {
                $table = '<div class="tableContainer">
                            <table title=\'tabella contenente i dettagli degli ingressi prenotati per il corso\'>
                                <caption>Dettaglio prenotazioni ingressi per il corso</caption>
                                <thead>
                                    <tr>
                                        <th scope=\'col\'>Utente</th>
                                        <th scope=\'col\'>Moto</th>
                                        <th scope=\'col\'>Attrezzatura</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <dettaglioCorsi/>
                                </tbody>
                            </table>
                        </div>';

                foreach($records as $record) {
                    $utente = $record['cognome'].' '.$record['nome'];

                    if ($record['moto'] == null)
                        $moto = 'Propria';
                    else
                    $moto = '#'.$record['moto'].' - '.$record['marca'].' '.$record['modello'].' '.$record['anno'];

                    if ($record['attrezzatura'] != 0)
                        $attrezzatura = 'Da noleggiare';
                    else
                        $attrezzatura = 'Propria';

                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th data-title=\'utente\' scope=\'row\'>'.$utente.'</th>';
                    $recordsBody .= '<td data-title=\'moto\'>'.$moto.'</td>';
                    $recordsBody .= '<td data-title=\'attrezzatura\'>'.$attrezzatura.'</td>';
                    $recordsBody .= '</tr>';
                }
            } else {
                $errorDetails = 'Non ci sono informazioni per le prenotazioni su questo corso.';
            }
        } catch (Throwable $t) {
            $errorDetails = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($globalError) > 0)
        $globalError = "<p class\"error\">$globalError</p>";

    if(strlen($errorDetails) > 0)
        $errorDetails = "<p class\"error\">$errorDetails</p>";

    $page = str_replace('_tabella_',$table,$page);
    $page = str_replace('_corso_',"#$id",$page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreDettagli/>',$errorDetails,$page);
    $page = str_replace('<dettaglioCorsi/>',$recordsBody,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>