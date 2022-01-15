<?php
    require_once('../../utils/db.php');

    use DB\dbAccess;

    $conn = new dbAccess();

    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login/');

    $records = array();

    if($conn->openDB()) {
        $records = $conn->getSpecificQueryResult(str_replace('_lezione_',$_GET['id'],dbAccess::QUERIES[13][0]),dbAccess::QUERIES[13][0]);

        $conn->closeDB();
    }

    echo json_encode($records);
?>