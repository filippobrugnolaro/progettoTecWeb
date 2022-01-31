<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');

    if (!isset($_GET['date']))
        header('Location: ./');

    $date = $_GET['date'];

    $page = file_get_contents('dettagliIngresso.html');
    $globalError = '';
    $errorDetails = '';
    $recordsBody = '';
    $table = '';

    $conn = new dbAccess();

    if ($conn->openDB()) {
        try {
            $records = $conn->getSpecificQueryResult(str_replace('_data_', $date, dbAccess::QUERIES[8][0]), dbAccess::QUERIES[8][1]);

            if ($records !== null) {
                $table = '<div class="tableContainer">
                                <table title=\'tabella contenente i dettagli degli ingressi prenotati per la giornata di apertura\'>
                                <caption>Dettaglio prenotazioni ingressi per la giornata di apertura</caption>
                                <thead>
                                    <tr>
                                        <th scope=\'col\'>Utente</th>
                                        <th scope=\'col\'>Moto</th>
                                        <th scope=\'col\'>Attrezzatura</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <dettaglioNoleggi/>
                                </tbody>
                            </table>
                        </div>';

                foreach ($records as $record) {
                    $utente = $record['cognome'] . ' ' . $record['nome'];

                    if ($record['moto'] == null)
                        $moto = 'Propria';
                    else
                        $moto = '#'.$record['moto'].' - '.$record['marca'].' '.$record['modello'].' '.$record['anno'];

                    if ($record['attrezzatura'] != 1)
                        $attrezzatura = 'Da noleggiare';
                    else
                        $attrezzatura = 'Propria';

                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th data-title=\'utente\' scope=\'row\'>' . $utente . '</th>';
                    $recordsBody .= '<td data-title=\'moto\'>' . $moto . '</td>';
                    $recordsBody .= '<td data-title=\'attrezzatura\'>' . $attrezzatura . '</td>';
                    $recordsBody .= '</tr>';
                }
            } else {
                $errorDetails = 'Non ci sono informazioni sulle prenotazioni di questa data di apertura.';
            }
        } catch (Throwable $t) {
            $errorDetails = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($globalError) > 0)
        $globalError = "<p class='error'>$globalError</p>";

    if(strlen($errorDetails) > 0)
        $errorDetails = "<p class='error'>$errorDetails</p>";

    $page = str_replace('_tabella_',$table, $page);
    $page = str_replace('_data_', '<time datetime=\''.$date.'\'>'.date('d/m/Y', strtotime($date)).'</time>', $page);
    $page = str_replace('<globalError/>', $globalError, $page);
    $page = str_replace('<erroreDettagli/>', $errorDetails, $page);
    $page = str_replace('<dettaglioNoleggi/>', $recordsBody, $page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>