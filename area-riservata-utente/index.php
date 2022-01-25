<?php
    require_once('../utils/db.php');
    require_once('../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../login/');

    $page = file_get_contents('home.html');

    $conn = new dbAccess();

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>
