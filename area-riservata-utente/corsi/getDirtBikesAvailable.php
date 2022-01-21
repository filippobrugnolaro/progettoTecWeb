<?php
    require_once('../../utils/db.php');

    use DB\dbAccess;

    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login/');

    $conn = new dbAccess();
    $records = array();

    if($conn->openDB()) {
        $lessons = $conn->getSpecificQueryResult(str_replace('_lezione_',$_GET['id'],dbAccess::QUERIES[13][0]),dbAccess::QUERIES[13][0]);
        $data = $lessons[0]['data'];
        unset($lessons);

        $records = $conn->getSpecificQueryResult(str_replace('_date_',$data,dbAccess::QUERIES[21][0]),dbAccess::QUERIES[21][0]);
        $conn->closeDB();
    }

    echo json_encode($records);
?>