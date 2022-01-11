<!--
<errors/>

<nextPrenotazioni/>

<nextLezioni/>
-->

<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login.php');


    $page = file_get_contents('home.html');

    $conn = new dbAccess();

   
    $globalError = '';

    $prenotazioni = '';
    $errorPrenotazioni = '';
    $lezioni = '';
    $errorLezioni = '';

    if ($conn->openDB()) {
        //get next n(?) track reservations 
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[---]); //da scrivere e da aggiungere all'array QUERIES
        
            if($records != null) {
                foreach($records as $record) {
                    $prenotazioni .= '<td>'.date("d/m/Y",strtotime($record['data']))'</td>';
                }
            }
        
        } catch (Throwable $t) {
            $errorPrenotazioni .= $t->getMessage();
        }

        //get next n(?) lessons
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[---]); //da scrivere e da aggiungere all'array QUERIES
        
            if($records != null) {
                foreach($records as $record) {
                    $lezioni .= '<td>'.date("d/m/Y",strtotime($record['data']))'</td>';
                }
            }
        
        } catch (Throwable $t) {
            $errorLezioni .= $t->getMessage();
        }


        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('img_path', '../'.$_SESSION['user']->getImgPath(), $page);
    $page = str_replace('<globalError/>',$globalError,$page);

    $page = str_replace('<nextPrenotazioni/>',$prenotazioni,$page);
    $page = str_replace('<errorPrenotazioni/>', $errorPrenotazioni, $page);

    $page = str_replace('<nextLezioni/>',$lezioni,$page);
    $page = str_replace('<errorLezioni/>', $errorLezioni, $page);

    echo $page;
?>


<!-- QUERIES 

-->

