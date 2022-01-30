<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('ingressi.html');

    $conn = new dbAccess();

    $globalError = '';
    $errorIngressi = '';
    $errorIngresso = '';
    $recordsBody = '';
    $ingressiBody = '';

    $tableIngressi = '';
    $tableIngresso = '';

    if ($conn->openDB()) {
        //get booked entries info
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[6]);

            if($records !== null) {
                $tableIngressi = '<div class="tableContainer">
                                    <table title=\'tabella contenente le prenotazioni degli ingressi per le prossime giornate di apertura\'>
                                        <caption>Prenotazioni ingressi date d\'apertura future</caption>
                                        <thead>
                                            <tr>
                                                <th scope=\'col\'>Data</th>
                                                <th scope=\'col\'>Posti disponibili</th>
                                                <th scope=\'col\'>Posti rimanenti</th>
                                                <th scope=\'col\'>Dettagli</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <ingressi/>
                                        </tbody>
                                    </table>
                                </div>';

                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th data-title=\'Data\' scope=\'row\'><time datetime=\''.$record['data'].'\'>'.date("d/m/Y",strtotime($record['data'])).'</time></th>';
                    $recordsBody .= '<td data-title=\'Posti disponibili\'>'.$record['posti'].'</td>';
                    $recordsBody .= '<td data-title=\'Posti rimanenti\'>'.($record['posti'] - $record['occupati']).'</td>';
                    $recordsBody .= '<td data-title=\'Dettagli\'><a href=\'dettagliIngresso.php?date='.$record['data'].'\' aria-label=\'dettaglio ingressi giornata\'><i class=\'fas fa-info-circle\'></i></a></td>';
                    $recordsBody .= '</tr>';
                }
            } else {
                $errorIngressi = 'Non ci sono ancora prenotazioni per le prossime date di apertura.';
            }

        } catch (Throwable $t) {
            $errorIngresso = $t->getMessage();
        }

        //get open days
        try {
            $ingressi = $conn->getQueryResult(dbAccess::QUERIES[7]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                $tableIngresso = '<div class="tableContainer">
                                    <table title=\'tabella contenente le prossime date di apertura\'>
                                        <caption>Prossime date di apertura</caption>
                                        <thead>
                                            <tr>
                                                <th scope=\'col\'>Data</th>
                                                <th scope=\'col\'>Giorno</th>
                                                <th scope=\'col\'>Posti disponibili</th>
                                                <th scope=\'col\'>Modifica</th>
                                                <th scope=\'col\'>Elimina</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <ingresso/>
                                        </tbody>
                                    </table>
                                </div>';

                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $ingressiBody .= '<tr>';
                    $ingressiBody .= '<th data-title=\'Data\' scope=\'row\'><time datetime=\''.$ingresso['data'].'\'>'.date('d/m/Y',strtotime($ingresso['data'])).'</time></th>';
                    $ingressiBody .= '<td data-title=\'giorno\'>'.$dw.'</td>';
                    $ingressiBody .= '<td data-title=\'Posti disponibili\'>'.$ingresso['posti'].'</td>';
                    $ingressiBody .= '<td data-title=\'modifica\'><a href=\'gestioneIngresso.php?date='.$ingresso['data'].'\' aria-label=\'modifica ingresso\'><i class=\'fas fa-pen\'></i></a></td>';
                    $ingressiBody .= '<td data-title=\'elimina\'><a href=\'deleteIngresso.php?date='.$ingresso['data'].'\' aria-label=\'elimina ingresso\'><i class=\'fas fa-trash\'></i></a></td>';
                    $ingressiBody .= '</tr>';
                }
            } else {
                $errorIngresso = 'Non ci sono ancora date di apertura disponibili.';
            }
        } catch (Throwable $t) {
            $errorMoto = $t->getMessage();
        }
        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($globalError) > 0)
        $globalError = '<p class=\'error\'>'.$globalError.'</p>';

    if(strlen($errorIngresso) > 0)
        $errorIngresso = '<p class=\'error\'>'.$errorIngresso.'</p>';

    if(strlen($errorIngressi) > 0)
        $errorIngressi = '<p class=\'error\'>'.$errorIngressi.'</p>';

    $page = str_replace('_tabellaIngressi_',$tableIngressi,$page);
    $page = str_replace('_tabellaIngresso_',$tableIngresso,$page);

    $page = str_replace('<erroreIngresso/>', $errorIngresso, $page);
    $page = str_replace('<erroreIngressi/>', $errorIngressi, $page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<ingressi/>',$recordsBody,$page);
    $page = str_replace('<ingresso/>',$ingressiBody,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>
