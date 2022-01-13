<?php
    require_once('../utils/db.php');
    require_once('../utils/user.php');
    require_once("../utils/utils.php");

    use DB\dbAccess;
    use USER\User;
    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

    // session_start();

    // if (isset($_SESSION['user'])) {
    //     switch(($_SESSION['user'])->getTipoUtente()) {
    //         case 1:
    //             header('Location: ../area-riservata-utente/');
    //             break;
    //         case 2:
    //             header('Location: ../area-riservata-admin/');
    //             break;
    //         default:
    //             session_destroy(); //should never happen
    //             break;

    //     }
    // }

    $page = file_get_contents("registrazione.html");
    $conn = new dbAccess();

    $errors = '';
    $globalError = "";
    $messaggiForm = '';

    $cognome = '';
    $nome = '';
    $nascita = '';
    $cf = '';
    $telefono = '';
    $email = '';
    
    if (isset($_POST['submit'])) {
        //check dati anagrafica    
        $cf = sanitizeInputString($_POST['cfUser']);
        if(strlen($cf) == 16) {
            switch(checkInputValidity($cf)) {
                case 1: $messaggiForm .= '<li>Codice fiscale non presente.</li>'; break;
                case 2: /*non dovrebbe succedere perche non c'e il pattern */ break;
                default: break;
            }
        } else {
            $messaggiForm .= '<li>Formato del Codice fiscale non corretto.</li>';
        }

        $cognome = sanitizeInputString($_POST['cognomeUser']);
        switch(checkInputValidity($cognome,'/^\p{L}+$/')) {
            case 1: $messaggiForm .= '<li>Cognome non presente.</li>'; break;
            case 2: $messaggiForm .= '<li>Il cognome non puo contenere numeri.</li>'; break;
            default: break;
        }

        $nome = sanitizeInputString($_POST['nomeUser']);
        switch(checkInputValidity($nome,'/^\p{L}+$/')) {
            case 1: $messaggiForm .= '<li>Nome non presente.</li>'; break;
            case 2: $messaggiForm .= '<li>Il nome non puo contenere numeri.</li>'; break;
            default: break;
        }

        $nascita = sanitizeInputString($_POST['nascitaUser']);
        switch(checkInputValidity($nascita,'/^\d{4}-\d{2}-\d{2}$/')) {
            case 1: $messaggiForm .= '<li>Data non presente.</li>'; break;
            case 2: $messaggiForm .= '<li>Formato data non corretto</li>'; break;
            default: break;
        }
        
        $telefono = sanitizeInputString($_POST['telUser']);
        if(ctype_digit($telefono)) {
            switch(checkInputValidity($telefono)) {
                case 1: $messaggiForm .= '<li>Telefono non presente.</li>'; break;
                case 2: /*non dovrebbe succedere perche non c'e il pattern */ break;
                default: break;
            }
        }
        
        
        //check email e password
        if (strlen($_POST['emailUser']) == 0) {
            $errors .= '<li>Email non inserita</li>';
        } else if (filter_var($_POST['emailUser'], FILTER_VALIDATE_EMAIL) == false) {
            $errors .= '<li>Email inserita non valida.</li>';
        } else {
            $email = sanitizeInputString($_POST['emailUser']);
        }

        if (strlen($_POST['pswUser']) == 0 || strlen($_POST['pswCheck']) == 0) {
            $errors .= '<li>Password non inserita.</li>';
        } else {
            if($_POST['pswUser'] != $_POST['pswCheck']) {
                $errors .= '<li>Le due password non combaciano.</li>';
            } else {
                $password = $_POST['pswUser'];
            }
        }

        if(strlen($errors) == 0) {
            $password = password_hash($password, PASSWORD_DEFAULT);

            //creo oggetto utente e faccio l'insert con la funzione
            $newUser = new User($cf, $nome, $cognome, $nascita, $telefono, $email, 0, $password);

            if($conn->openDB()) {
                if($conn->createNewUser($newUser) > -1) {
                    header('Location: ../login/');
                } else {
                    $globalError = 'Errore durante l\'inserimento della registrazione.';
                }
                $conn->closeDB();
            } else {
                $globalError = 'Errore di connessione, riprovare più tardi.';
            }
            
        }
    }

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);
    $page = str_replace('<globalError/>', $globalError, $page);

    $page = str_replace("_cognome_", $cognome, $page);
    $page = str_replace("_nome_", $nome, $page);
    $page = str_replace("_nascita_", $nascita, $page);
    $page = str_replace("_cf_", $cf, $page);
    $page = str_replace("_telefono_", $telefono, $page);

    $page = str_replace('<errors/>', $errors, $page);

    $page = str_replace("_email_", $email, $page); 

    echo $page;
?>