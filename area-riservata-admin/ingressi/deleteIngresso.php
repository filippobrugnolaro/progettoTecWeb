<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');

    if(!isset($_GET['date']))
        header('Location: ./');

    $date = $_GET['date'];

    $conn = new dbAccess();

    if($conn->openDB()) {
        $conn->deleteEntry($date);
        $conn->closeDB();
    }

    header('Location: ./#gestioneIngressi');

?>