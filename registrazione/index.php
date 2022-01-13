<?php
    require_once('../utils/db.php');
    require_once('../utils/user.php');
    require_once("../utils/utils.php");

    use DB\dbAccess;
    use function UTILS\sanitizeInputString;

    session_start();

    if (isset($_SESSION['user'])) {
        switch(($_SESSION['user'])->getTipoUtente()) {
            case 1:
                header('Location: ../area-riservata-utente/');
                break;
            case 2:
                header('Location: ../area-riservata-admin/');
                break;
            default:
                session_destroy(); //should never happen
                break;

        }
    }

    $page = file_get_contents("registrazione.html");
    $conn = new dbAccess();

    $errors = "";

    if (isset($_POST['submit'])) {
        //check dati anagrafica    
        $cf = sanitizeInputString($_POST['cfUser']);
        $cognome = sanitizeInputString($_POST['cognomeUser']);
        $nome = sanitizeInputString($_POST['nomeUser']);
        $nascita = sanitizeInputString($_POST['nascitaUser']);
        $telefono = sanitizeInputString($_POST['telUser']);


        
        
        //check email e password
        if (strlen($_POST['email']) == 0) {
            $errors .= '<li>Email non inserita</li>';
        } else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
            $errors .= '<li>Email inserita non valida.</li>';
        } else {
            $email = sanitizeInputString($_POST['email']);
        }

        if (strlen($_POST['password']) == 0) {
            $errors .= '<li>Password non inserita</li>';
        } else
            $password = $_POST['password'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        //creo oggetto e lo inserisco con la funzione

        
    }

    $page = str_replace('<messaggiForm/>', $errors, $page);

    echo $page;
?>