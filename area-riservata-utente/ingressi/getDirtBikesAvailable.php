<?php
    require_once('../../utils/db.php');

    use DB\dbAccess;

    $conn = new dbAccess();

    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login/');

    $records = array();

    if($conn->openDB()) {
        $records = $conn->getSpecificQueryResult(str_replace('_date_',$_GET['data'],dbAccess::QUERIES[21][0]),dbAccess::QUERIES[21][0]);

        $conn->closeDB();
    }

    echo json_encode($records);
?>