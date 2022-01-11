<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login.php');


    $page = file_get_contents('lezioni.html');

    $conn = new dbAccess();

   
    $globalError = '';

    $lezioni = '';
    $errorLezione = '';
    
    $calendarioLezioni = '';
    $errorCalendario = '';

    $lezioniDropdown = '';

    $cfUtente = $_SESSION['user']->getCF();

    if ($conn->openDB()) {
        //get next n lessons reservations
        try {
            $ingressi = $conn->getQueryResult(dbAccess::QUERIES[---]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $lezioni .= '<tr>';
                    $lezioni .= '<td scope=\'row\'>'.date('d/m/Y',strtotime($ingresso['data'])).'</td>';
                    $lezioni .= '<td>'.$dw.'</td>';
                    $lezioni .= '<td>'.$ingresso['posti'].'</td>';
                    $lezioni .= '<td><a href=\'deletePrenotazione.php?date='.$ingresso['data'].'\' aria-label=\'elimina ingresso\'><i class=\'fas fa-trash\'></i></a></td>';
                    $lezioni .= '</tr>';
                }
            }
        } catch (Throwable $t) {
            $errorLezione = $t->getMessage();
        }

        //get next n dates
        try {
            $ingressi = $conn->getQueryResult(dbAccess::QUERIES[7]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $calendarioLezioni .= '<tr>';
                    $calendarioLezioni .= '<td scope=\'row\'>'.$ingresso['data'].'</td>';
                    $calendarioLezioni .= '<td>'.$dw.'</td>';
                    $calendarioLezioni .= '<td>'.$ingresso['posti'].'</td>';
                    $calendarioLezioni .= '</tr>';

                    $lezioniDropdown .= '<option value="'.$ingresso['data'].'">'.$ingresso['data'].'</option>'
                }
            }
        
        } catch (Throwable $t) {
            $errorCalendario .= $t->getMessage();
        }


        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('img_path', '../'.$_SESSION['user']->getImgPath(), $page);
    $page = str_replace('<globalError/>',$globalError,$page);

    $page = str_replace('<nextLezioni/>',$lezioni,$page);
    $page = str_replace('<erroreLezione/>', $errorLezione, $page);
    
    $page = str_replace('<nextDate/>',$calendarioLezioni,$page);
    $page = str_replace('<erroreNextDate/>', $errorCalendario, $page);

    $page = str_replace('[cfUtente]',$cfUtente,$page);
    $page = str_replace('<dataDisp/>',$lezioniDropdown,$page);

    echo $page;
?>


<!-- QUERIES 

-->