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

    if ($conn->openDB()) {
        //get rent infos

        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[2]);

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<td scope=\'row\'>'.date("d/m/Y",strtotime($record['data'])).'</td>'; //controllare accessibilità
                    $recordsBody .= '<td>'.$record['totNoleggi'].'</td>';
                    $recordsBody .= '<td><a href=\'dettagliNoleggio.php?date='.$record['data'].'\' aria-label=\'dettaglio noleggi giornata\'><i class=\'fas fa-info-circle\'></i></a></td>';
                    $recordsBody .= '</tr>';
                }
            }

        } catch (Throwable $t) {
            $errorNoleggio = $t->getMessage();
        }

        //get rentable dirtbikes from db
        try {
            $motos = $conn->getQueryResult(dbAccess::QUERIES[0]);

            if($motos !== null) {
                foreach($motos as $moto) {
                    $motoBody .= '<tr>';
                    $motoBody .= '<td scope=\'row\'>'.$moto['numero'].'</td>';
                    $motoBody .= '<td>'.$moto['marca'].'</td>';
                    $motoBody .= '<td>'.$moto['modello'].'</td>';
                    $motoBody .= '<td>'.$moto['cilindrata'].'cc</td>';
                    $motoBody .= '<td>'.$moto['anno'].'</td>';
                    $motoBody .= '<td><a href=\'gestioneMoto.php?id='.$moto['numero'].'\' aria-label=\'modifica moto\'><i class=\'fas fa-pen\'></i></a></td>';
                    $motoBody .= '<td><a href=\'deleteMoto.php?id='.$moto['numero'].'\' aria-label=\'elimina moto\'><i class=\'fas fa-trash\'></i></a></td>';
                    $motoBody .= '</tr>';
                }
            }
        } catch (Throwable $t) {
            $errorMoto = $t->getMessage();
        }
        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    //$page = str_replace('img_path', '../'.$_SESSION['user']->getImgPath(), $page);
    $page = str_replace('<erroreMoto/>', $errorMoto, $page);
    $page = str_replace('<erroreNoleggio/>', $errorNoleggio, $page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<moto/>',$motoBody,$page);
    $page = str_replace('<noleggi/>',$recordsBody,$page);

    echo $page;
?>
