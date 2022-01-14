<?php
    require_once('../utils/db.php');
    require_once('../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../login/');


    $page = file_get_contents('home.html');

    $conn = new dbAccess();


    $globalError = '';

    $prenotazioni = '';
    $errorPrenotazioni = '';
    $lezioni = '';
    $errorLezioni = '';

    $cfUtente = $_SESSION['user']->getCF();

    if ($conn->openDB()) {
        //get next 3 track reservations
        try {
            $ingressi = $conn->getSpecificQueryResult(str_replace('_cfUser_',$cfUtente, dbAccess::QUERIES[15][0]), dbAccess::QUERIES[15][1]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $prenotazioni .= '<tr>';
                    $prenotazioni .= '<td scope=\'row\'>'.date('d/m/Y',strtotime($ingresso['data'])).'</td>';
                    $prenotazioni .= '<td>'.$dw.'</td>';
                    $prenotazioni .= '</tr>';
                }
            }
        } catch (Throwable $t) {
            $errorPrenotazioni = $t->getMessage();
        }

        //get next 3 lessons reservations
        try {
            $ingressi = $conn->getSpecificQueryResult(str_replace('_cfUser_', $cfUtente, dbAccess::QUERIES[16][0]), dbAccess::QUERIES[16][1]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $lezioni .= '<tr>';
                    $lezioni .= '<td scope=\'row\'>'.date('d/m/Y',strtotime($ingresso['data'])).'</td>';
                    $lezioni .= '<td>'.$dw.'</td>';
                    $lezioni .= '</tr>';
                }
            }
        } catch (Throwable $t) {
            $errorLezione = $t->getMessage();
        }


        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('<globalError/>',$globalError,$page);

    $page = str_replace('<nextPrenotazioni/>',$prenotazioni,$page);
    $page = str_replace('<errorPrenotazioni/>', $errorPrenotazioni, $page);

    $page = str_replace('<nextLezioni/>',$lezioni,$page);
    $page = str_replace('<errorLezioni/>', $errorLezioni, $page);

    echo $page;
?>


<!-- QUERIES

-->
