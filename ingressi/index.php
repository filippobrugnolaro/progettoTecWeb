<?php
    require_once('../utils/db.php');

    use DB\dbAccess;

    $page = file_get_contents('ingressi.html');

    echo $page;
?>