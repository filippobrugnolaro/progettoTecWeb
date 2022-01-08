<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login.php');

    if(!isset($_GET['date']))
        header('Location: ./');

    $date = $_GET['date'];

    $page = file_get_contents('dettagliIngresso.html');
    $globalError = '';
    $errorDetails = '';
    $recordsBody = '';

    $conn = new dbAccess();

    if($conn->openDB()) {
        try {
            $records = $conn->getSpecificQueryResult(str_replace('_data_',$date,dbAccess::QUERIES[8][0]),dbAccess::QUERIES[8][1]);

            if($records !== null)
                foreach($records as $record) {
                    $utente = $record['cognome'].' '.$record['nome'];

                    if($record['moto'] == null) {
                        $moto = 'Propria';
                        $attrezzatura = 'Propria';
                    } else {
                        $moto = '#'.$record['moto'];

                        if($record['attrezzatura'] != null)
                            $attrezzatura = 'Da noleggiare';
                        else
                            $attrezzatura = 'Proprio';
                    }

                    $recordsBody .= '<tr>';
                    $recordsBody .= '<td scope=\'row\'>'.$utente.'</td>';
                    $recordsBody .= '<td>'.$moto.'</td>';
                    $recordsBody .= '<td>'.$attrezzatura.'</td>';
                    $recordsBody .= '</tr>';
                }
        } catch (Throwable $t) {
            $errorDetails = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('img_path', "../".$_SESSION['user']->getImgPath(), $page);
    $page = str_replace('_data_',date('d/m/Y',strtotime($date)),$page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreDettagli>',$errorDetails,$page);
    $page = str_replace('<dettaglioNoleggi/>',$recordsBody,$page);

    echo $page;
?>