<?php
    require_once('../utils/user.php');

    session_start();

    if(!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../login/');

    echo file_get_contents('Home.html');

?>