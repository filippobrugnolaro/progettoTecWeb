<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('noleggio.html');

    $conn = new dbAccess();

    $globalError = '';
    $errorNoleggio = '';
    $errorMoto = '';
    $recordsBody = '';
    $motoBody = '';

    $tableNoleggi = '';
    $tableNoleggio = '';

    if ($conn->openDB()) {
        //get rent infos
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[2]);

            if($records !== null) {
                $tableNoleggi = '<div class="tableContainer">
                                    <table title="tabella contenente le prenotazioni dei noleggi per le prossime giornate di apertura">
                                        <caption>Prenotazioni noleggi prossime date d\'apertura</caption>
                                        <thead>
                                            <tr>
                                                <th scope="col">Data</th>
                                                <th scope="col">Noleggi totali</th>
                                                <th scope="col">Dettagli</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <noleggi/>
                                        </tbody>
                                    </table>
                                </div>';

                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th data-title=\'data\' scope=\'row\'><time datetime=\''.$record['data'].'\'>'.date("d/m/Y",strtotime($record['data'])).'</time></th>'; //controllare accessibilità
                    $recordsBody .= '<td data-title=\'noleggi totali\'>'.$record['totNoleggi'].'</td>';
                    $recordsBody .= '<td data-title=\'dettagli\'><a href=\'dettagliNoleggio.php?date='.$record['data'].'\' aria-label=\'dettaglio noleggi giornata\'><i class=\'fas fa-info-circle\'></i></a></td>';
                    $recordsBody .= '</tr>';
                }
            } else {
                $errorNoleggio = 'Non ci sono informazioni sui noleggi delle prossime date di apertura.';
            }

        } catch (Throwable $t) {
            $errorNoleggio = $t->getMessage();
        }

        //get rentable dirtbikes from db
        try {
            $motos = $conn->getQueryResult(dbAccess::QUERIES[0]);

            if($motos !== null) {
                $tableNoleggio = '<div class="tableContainer">
                                    <table title="tabella contenente le moto del magazzino">
                                        <caption>Moto del magazzino</caption>
                                        <thead>
                                            <tr>
                                                <th scope="col">Identificativo</th>
                                                <th scope="col">Marca</th>
                                                <th scope="col">Modello</th>
                                                <th scope="col">Cilindrata</th>
                                                <th scope="col">Anno</th>
                                                <th scope="col">Modifica</th>
                                                <th scope="col">Elimina</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <moto/>
                                        </tbody>
                                    </table>
                                </div>';

                foreach($motos as $moto) {
                    $motoBody .= '<tr>';
                    $motoBody .= '<th data-title=\'ID\' scope=\'row\'><span aria-hidden=\'true\'>#</span>'.$moto['numero'].'</th>';
                    $motoBody .= '<td data-title=\'marca\'>'.$moto['marca'].'</td>';
                    $motoBody .= '<td data-title=\'modello\'>'.$moto['modello'].'</td>';
                    $motoBody .= '<td data-title=\'cilindrata\'>'.$moto['cilindrata'].'<abbr title=\'Centimetri cubici\'>cc</abbr></td>';
                    $motoBody .= '<td data-title=\'anno\'>'.$moto['anno'].'</td>';
                    $motoBody .= '<td data-title=\'modifica\'><a href=\'gestioneMoto.php?id='.$moto['numero'].'\' aria-label=\'modifica moto\'><i class=\'fas fa-pen\'></i></a></td>';
                    $motoBody .= '<td data-title=\'elimina\'><a href=\'deleteMoto.php?id='.$moto['numero'].'\' aria-label=\'elimina moto\'><i class=\'fas fa-trash\'></i></a></td>';
                    $motoBody .= '</tr>';
                }
            } else {
                $errorMoto = 'Non sono ancora state inserite moto a noleggio.';
            }
        } catch (Throwable $t) {
            $errorMoto = $t->getMessage();
        }
        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';


    if(strlen($globalError) > 0)
        $globalError = '<p class=\'error\'>'.$globalError.'</p>';

    if(strlen($errorMoto) > 0)
        $errorMoto = '<p class=\'error\'>'.$errorMoto.'</p>';

    if(strlen($errorNoleggio) > 0)
        $errorNoleggio = '<p class=\'error\'>'.$errorNoleggio.'</p>';


    $page = str_replace('<erroreMoto/>', $errorMoto, $page);
    $page = str_replace('<erroreNoleggio/>', $errorNoleggio, $page);
    $page = str_replace('<globalError/>',$globalError,$page);

    $page = str_replace('_prenotazioneNoleggio_',$tableNoleggi,$page);
    $page = str_replace('_motoNoleggio_',$tableNoleggio,$page);

    $page = str_replace('<moto/>',$motoBody,$page);
    $page = str_replace('<noleggi/>',$recordsBody,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>
