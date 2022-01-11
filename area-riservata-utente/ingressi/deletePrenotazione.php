<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login.php');

    if(!isset($_GET['date']))
        header('Location: ./');

    $date = $_GET['date'];

    $conn = new dbAccess();

    if($conn->openDB()) {
        $conn->deleteReservation($date);
        $conn->closeDB();
    }

    header('Location: ./#gestioneIngressi');

?>