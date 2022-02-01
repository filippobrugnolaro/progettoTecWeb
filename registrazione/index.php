<?php
    require_once('../utils/db.php');
    require_once('../utils/user.php');
    require_once("../utils/utils.php");

    use DB\dbAccess;
    use USER\User;
    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

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
    $username = '';

    if (isset($_POST['submit'])) {
        //check dati anagrafica
        $cf = sanitizeInputString($_POST['cfUser']);
        switch(checkInputValidity($cf,'/^(?:[A-Z][AEIOU][AEIOUX]|[AEIOU]X{2}|[B-DF-HJ-NP-TV-Z]{2}[A-Z]){2}(?:[\dLMNP-V]{2}(?:[A-EHLMPR-T](?:[04LQ][1-9MNP-V]|[15MR][\dLMNP-V]|[26NS][0-8LMNP-U])|[DHPS][37PT][0L]|[ACELMRT][37PT][01LM]|[AC-EHLMPR-T][26NS][9V])|(?:[02468LNQSU][048LQU]|[13579MPRTV][26NS])B[26NS][9V])(?:[A-MZ][1-9MNP-V][\dLMNP-V]{2}|[A-M][0L](?:[1-9MNP-V][\dLMNP-V]|[0L][1-9MNP-V]))[A-Z]$/')) {
                case 1: $messaggiForm .= '<li>Codice fiscale non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Codice fiscale non valido.</li>'; break;
                default: break;
            }

        $username = sanitizeInputString($_POST['username']);
            switch(checkInputValidity($username,'/^(?=.{4,10}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9]+(?<![_.])$/')) {
                case 1: $messaggiForm .= '<li><span lang="en">Username</span> non presente.</li>'; break;
                case 2: $messaggiForm .= '<li><span lang="en">Username</span> deve contenere tra i 4 e i 10 caratteri, solo lettere minuscole e numeri.</li>'; break;
                default: break;
            }

        $cognome = sanitizeInputString($_POST['cognomeUser']);
        switch(checkInputValidity($cognome,'/^\p{L}+$/')) {
            case 1: $messaggiForm .= '<li>Cognome non presente.</li>'; break;
            case 2: $messaggiForm .= '<li>Il cognome non puo contenere numeri o caratteri speciali.</li>'; break;
            default: break;
        }

        if(strlen($cognome) > 0)
        $cognome[0] = strtoupper($cognome[0]);

        $nome = sanitizeInputString($_POST['nomeUser']);
        switch(checkInputValidity($nome,'/^\p{L}+$/')) {
            case 1: $messaggiForm .= '<li>Nome non presente.</li>'; break;
            case 2: $messaggiForm .= '<li>Il nome non puo contenere numeri o caratteri speciali.</li>'; break;
            default: break;
        }

        if(strlen($nome) > 0)
            $nome[0] = strtoupper($nome[0]);

        $nascita = sanitizeInputString($_POST['nascitaUser']);
        switch(checkInputValidity($nascita,'/^\d{4}-\d{2}-\d{2}$/')) {
            case 1: $messaggiForm .= '<li>Data di nascita non presente.</li>'; break;
            case 2: $messaggiForm .= '<li>Formato data non corretto</li>'; break;
            default: break;
        }

        if(strtotime($nascita) > strtotime(date("Y-m-d")))
            $messaggiForm .= '<li>Data di nascita deve essere antecedente alla data odierna.</li>';

        $telefono = sanitizeInputString($_POST['telUser']);
            switch(checkInputValidity($telefono,'/^\d{8,10}$/')) {
                case 1: $messaggiForm .= '<li>Telefono non presente.</li>'; break;
                case 2: $messaggiForm .= '<li>Telefono può avere tra le 8 e le 10 cifre.</li>'; break;
                default: break;
            }

        //check email e password
        if (strlen($_POST['emailUser']) == 0) {
            $errors .= '<li><span lang="en">E-mail</span> non inserita</li>';
        } else if (filter_var($_POST['emailUser'], FILTER_VALIDATE_EMAIL) == false) {
            $errors .= '<li><span lang="en">E-mail</span> inserita non valida.</li>';
        } else {
            $email = sanitizeInputString($_POST['emailUser']);
        }

        if (strlen($_POST['pswUser']) == 0 || strlen($_POST['pswCheck']) == 0) {
            $errors .= '<li>Password o Conferma password non inserite.</li>';
        } else {
            if($_POST['pswUser'] != $_POST['pswCheck']) {
                $errors .= '<li>Le due password non combaciano.</li>';
            } else {
                $password = $_POST['pswUser'];
            }
        }

        if(strlen($errors) == 0 && strlen($messaggiForm) == 0) {
            $password = password_hash($password, PASSWORD_DEFAULT);

            //creo oggetto utente e faccio l'insert con la funzione
            $newUser = new User($cf, $nome, $cognome, $nascita, $telefono, $email, 0, $username, $password);

            if($conn->openDB()) {
                $res = $conn->createNewUser($newUser);

                if($res > -1) {
                    header('Location: ../login/');
                } else if($res == -1){
                    $globalError = 'Errore durante l\'inserimento della registrazione.';
                } else {
                    $globalError = 'Esiste già un utente con questo codice fiscale o con questo <span lang="en">username</span>.';
                }
                $conn->closeDB();
            } else {
                $globalError = 'Errore di connessione, riprovare più tardi.';
            }

        }
    }

    if(strlen($messaggiForm) > 0)
        $messaggiForm = "<ul>$messaggiForm</ul>";

    if(strlen($globalError) > 0)
        $globalError = "<p class=\"error\">$globalError</p>";

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);
    $page = str_replace('<globalError/>', $globalError, $page);

    $page = str_replace('_cognome_', $cognome, $page);
    $page = str_replace('_nome_', $nome, $page);
    $page = str_replace('_nascita_', $nascita, $page);
    $page = str_replace('_cf_', $cf, $page);
    $page = str_replace('_telefono_', $telefono, $page);
    $page = str_replace('_username_',$username,$page);

    $page = str_replace('<errors/>', $errors, $page);

    $page = str_replace('_email_', $email, $page);

    echo $page;
?>