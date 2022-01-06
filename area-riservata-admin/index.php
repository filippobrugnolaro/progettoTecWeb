<?php
    require_once('../utils/user.php');

    session_start();

    if(!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../login.php');


    $page = file_get_contents('dashboard.html');
    $page = str_replace('img_path',$_SESSION['user']->getImgPath(),$page);

    echo $page;

?>