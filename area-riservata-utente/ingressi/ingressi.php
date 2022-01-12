<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login.php');


    $page = file_get_contents('ingressi.html');

    $conn = new dbAccess();

   
    $globalError = '';

    $prenotazioni = '';
    $errorPrenotazione = '';
    
    $ingressiBody = '';
    $errorIngresso = '';

    $ingressiDropdown = '';

    $cfUtente = $_SESSION['user']->getCF();

    if ($conn->openDB()) {
        //get next n track reservations
        try {
            $ingressi = $conn->getSpecificQueryResult(str_replace('_cfUser_', $cfUtente, dbAccess::QUERIES[15][0]), dbAccess::QUERIES[15][1]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $prenotazioni .= '<tr>';
                    $prenotazioni .= '<td scope=\'row\'>'.date('d/m/Y',strtotime($ingresso['data'])).'</td>';
                    $prenotazioni .= '<td>'.$dw.'</td>';
                    // $prenotazioni .= '<td>'.$ingresso['posti'].'</td>';
                    $prenotazioni .= '<td><a href=\'deletePrenotazione.php?id='.$ingresso['id'].'\' aria-label=\'elimina ingresso\'><i class=\'fas fa-trash\'></i></a></td>';
                    $prenotazioni .= '</tr>';
                }
            }
        } catch (Throwable $t) {
            $errorPrenotazione = $t->getMessage();
        }

        //get next n dates
        try {
            $ingressi = $conn->getQueryResult(dbAccess::QUERIES[7]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $ingressiBody .= '<tr>';
                    $ingressiBody .= '<td scope=\'row\'>'.$ingresso['data'].'</td>';
                    $ingressiBody .= '<td>'.$dw.'</td>';
                    $ingressiBody .= '<td>'.$ingresso['posti'].'</td>';
                    $ingressiBody .= '</tr>';

                    $ingressiDropdown .= '<option value="'.$ingresso['data'].'">'.$ingresso['data'].'</option>'
                }
            }
        
        } catch (Throwable $t) {
            $errorIngresso .= $t->getMessage();
        }


        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('img_path', '../'.$_SESSION['user']->getImgPath(), $page);
    $page = str_replace('<globalError/>',$globalError,$page);

    $page = str_replace('<nextPrenotazioni/>',$prenotazioni,$page);
    $page = str_replace('<errorePrenotazione/>', $errorPrenotazione, $page);
    
    $page = str_replace('<nextDate/>',$ingressiBody,$page);
    $page = str_replace('<erroreIngresso/>', $errorIngresso, $page);

    $page = str_replace('[cfUtente]',$cfUtente,$page);
    $page = str_replace('<dataDisp/>',$ingressiDropdown,$page);

    echo $page;
?>

<!-- QUERIES 

-->
