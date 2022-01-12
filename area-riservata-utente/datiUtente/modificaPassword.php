<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login.php');

    // if(!isset($_GET['date']))
    //     header('Location: ./');

    $page = file_get_contents("datiUtente.html");
    $conn = new dbAccess();

    $oldPsw = $_POST['oldPassword'];
    $newPsw = $_POST['newPassword'];

    $errors = '';
    $globalError = '';



    if($conn->openDB()) {
        if($conn->updateUserPsw($oldPsw, $newPsw)) { /* */

        } else
            $errors .= ''

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    header('Location: ./#datiPersonali');

?>