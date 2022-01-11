<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;
    use function UTILS\sanitizeInputString;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login.php');

    $page = file_get_contents("datiUtente.html");
    $conn = new dbAccess();

    $errors = "";
    $globalError = '';
    $utenteDB = "";
    $utenteHTML = "";

    if($conn->openDB()) {
        
        try {
            $utenteDB = getDatiUtente(); //da creare funzione, o da usare la classe user
        
            //costruzione di <datiUtente/>

            // --- TO BE DONE
        
        } catch (Throwable $t) {
            $errors .= $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';
    



    //sostituisco il placeholder nella pagina HTML con il codice
    echo str_replace("<datiUtente/>", $utenteHTML, $page);

?>
