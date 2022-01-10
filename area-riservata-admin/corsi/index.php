<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login.php');


    $page = file_get_contents('corsi.html');

    $conn = new dbAccess();

    $globalError = '';
    $errorCorsi = '';
    $errorCorso = '';
    $recordsBody = '';
    $corsiBody = '';

    if ($conn->openDB()) {
        //get booked lessons infos
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[10]);

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<td scope=\'row\'>#'.$record['id'].'</td>';
                    $recordsBody .= '<td>'.date("d/m/Y",strtotime($record['data'])).'</td>'; //controllare accessibilità
                    $recordsBody .= '<td>'.$record['posti'].'</td>';
                    $recordsBody .= '<td>'.($record['posti'] - $record['occupati']).'</td>';
                    $recordsBody .= '<td><a href=\'dettagliCorso.php?id='.$record['id'].'\' aria-label=\'dettaglio corso\'><i class=\'fas fa-info-circle\'></i></a></td>';
                    $recordsBody .= '</tr>';
                }
            }
        } catch (Throwable $t) {
            $errorCorsi = $t->getMessage();
        }

        //get lessons infos
        try {
            $corsi = $conn->getQueryResult(dbAccess::QUERIES[11]);

            if($corsi !== null) {
                foreach($corsi as $corso) {
                    $corsiBody .= '<tr>';
                    $corsiBody .= '<td scope=\'row\'>'.date('d/m/Y',strtotime($corso['data'])).'</td>';
                    $corsiBody .= '<td>'.$corso['posti'].'</td>';
                    $corsiBody .= '<td>'.$corso['istruttore'].'</td>';
                    $corsiBody .= '<td>#'.$corso['pista'].'</td>';
                    $corsiBody .= '<td><a href=\'gestioneCorso.php?id='.$corso['id'].'\' aria-label=\'modifica corso\'><i class=\'fas fa-pen\'></i></a></td>';
                    $corsiBody .= '<td><a href=\'deleteCorso.php?id='.$corso['id'].'\' aria-label=\'elimina corso\'><i class=\'fas fa-trash\'></i></a></td>';
                    $corsiBody .= '</tr>';
                }
            }
        } catch (Throwable $t) {
            $errorCorso = $t->getMessage();
        }
        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('img_path', '../'.$_SESSION['user']->getImgPath(), $page);
    $page = str_replace('<erroreCorso/>', $errorCorso, $page);
    $page = str_replace('<erroreCorsi/>', $errorCorsi, $page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<corsi/>',$recordsBody,$page);
    $page = str_replace('<corso/>',$corsiBody,$page);

    echo $page;
?>
