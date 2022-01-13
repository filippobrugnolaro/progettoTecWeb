<?php
    require_once('../utils/db.php');

    use DB\dbAccess;

    $page = file_get_contents('corsi.html');

    echo $page;
?>