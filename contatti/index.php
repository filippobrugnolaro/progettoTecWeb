<?php
    require_once('../utils/db.php');

    use DB\dbAccess;

    $page = file_get_contents('contatti.html');

    echo $page;
?>