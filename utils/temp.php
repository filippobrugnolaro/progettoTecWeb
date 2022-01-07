<?php
    $psw = password_hash('ale',PASSWORD_DEFAULT);

    $conn = mysqli_connect('127.0.0.1','acavalie','aexie7Aht6aut3uo','acavalie');

    mysqli_query($conn,"UPDATE utente SET password = '$psw' WHERE cf = 'CVLLSN00A04A001A'") or die (mysqli_error($conn));
?>