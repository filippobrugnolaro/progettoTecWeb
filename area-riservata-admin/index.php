<?php
    require_once('../utils/user.php');

    session_start();

    if(!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../login/');


    $page = file_get_contents('dashboard.html');
    //$page = str_replace('img_path','../user-images/'.$_SESSION['user']->getImgPath(),$page);

    echo $page;

?>