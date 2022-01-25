<?php
    require_once('../utils/user.php');

    session_start();

    if(!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../login/');

    $page = file_get_contents('dashboard.html');
    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;

?>