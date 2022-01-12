<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login.php');

    if(!isset($_GET['id']))
        header('Location: ./');

    $id = $_GET['id'];

    $conn = new dbAccess();

    if($conn->openDB()) {
        $conn->deleteReservation($id);
        $conn->closeDB();
    }

    header('Location: ./#gestioneIngressi');

?>