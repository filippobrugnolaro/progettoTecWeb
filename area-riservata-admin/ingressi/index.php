<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login.php');


    $page = file_get_contents('ingressi.html');

    $conn = new dbAccess();

    $globalError = '';
    $errorIngressi = '';
    $errorIngresso = '';
    $recordsBody = '';
    $ingressiBody = '';

    if ($conn->openDB()) {
        //get booked entries info
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[6]);

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<td scope=\'row\'>'.date("d/m/Y",strtotime($record['data'])).'</td>'; //controllare accessibilità
                    $recordsBody .= '<td>'.$record['posti'].'</td>';
                    $recordsBody .= '<td>'.($record['posti'] - $record['occupati']).'</td>';
                    $recordsBody .= '<td><a href=\'dettagliIngresso.php?date='.$record['data'].'\' aria-label=\'dettaglio ingressi giornata\'><i class=\'fas fa-info-circle\'></i></a></td>';
                    $recordsBody .= '</tr>';
                }
            }

        } catch (Throwable $t) {
            $errorIngresso = $t->getMessage();
        }

        //get open days
        try {
            $ingressi = $conn->getQueryResult(dbAccess::QUERIES[7]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                foreach($ingressi as $ingresso) {
                    //echo date('w',strtotime($ingresso['data']));
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $ingressiBody .= '<tr>';
                    $ingressiBody .= '<td scope=\'row\'>'.$ingresso['data'].'</td>';
                    $ingressiBody .= '<td>'.$dw.'</td>';
                    $ingressiBody .= '<td>'.$ingresso['posti'].'</td>';
                    $ingressiBody .= '<td><a href=\'gestioneIngresso.php?date='.$ingresso['data'].'\' aria-label=\'modifica ingresso\'><i class=\'fas fa-pen\'></i></a></td>';
                    $ingressiBody .= '<td><a href=\'deleteIngresso.php?date='.$ingresso['data'].'\' aria-label=\'elimina ingresso\'><i class=\'fas fa-trash\'></i></a></td>';
                    $ingressiBody .= '</tr>';
                }
            }
        } catch (Throwable $t) {
            $errorMoto = $t->getMessage();
        }
        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('img_path', '../'.$_SESSION['user']->getImgPath(), $page);
    $page = str_replace('<erroreIngresso/>', $errorIngresso, $page);
    $page = str_replace('<erroreIngressi/>', $errorIngressi, $page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<ingressi/>',$recordsBody,$page);
    $page = str_replace('<ingresso/>',$ingressiBody,$page);

    echo $page;
?>
