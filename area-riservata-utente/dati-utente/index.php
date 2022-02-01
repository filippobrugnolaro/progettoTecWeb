<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');
    require_once('../../utils/utils.php');

    use DB\dbAccess;
    use USER\User;
    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login/');

    $page = file_get_contents("datiUtente.html");
    $conn = new dbAccess();

    $errors = '';
    $globalError = "";
    $messaggiForm = '';

    $cognome = '';
    $nome = '';
    $nascita = '';
    $telefono = '';

    $username = '';
    $email = '';
    $password = '';

    // PRIMO FORM - Modifica anagrafica
    if (isset($_POST['submitUser'])) {
        //check dati anagrafica
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

        if(strlen($messaggiForm) == 0) {
            //prendo il cf per identificare l'utente
            $cf = $_SESSION['user']->getCF();

            //creo oggetto utente e faccio l'insert con la funzione
            $newUser = new User($cf, $nome, $cognome, $nascita, $telefono, $_SESSION['user']->getEmail(), 1,$_SESSION['user']->getUserName(), $_SESSION['user']->getPsw());

            if($conn->openDB()) {
                if($conn->updateUserData($newUser)) {
                    $_SESSION['user'] = $newUser;
                    $messaggiForm = '<li>Dati personali modificati con successo.</li>';
                } else {
                    $globalError = 'Errore durante l\'aggiornamento dei dati.';
                }
                $conn->closeDB();
            } else {
                $globalError = 'Errore di connessione, riprovare più tardi.';
            }
        }
        $email = $_SESSION['user']->getEmail();

    //SECONDO FORM - Modifica Password
    } else if (isset($_POST['submitPsw'])) {
        $email = $_SESSION['user']->getEmail();
        $username = $_SESSION['user']->getUserName();

        //check password
        if (strlen($_POST['oldPsw']) == 0) {
            $errors .= '<li>Vecchia password non inserita.</li>';
        } else {
            $oldPassword = $_POST['oldPsw'];
        }

        if (strlen($_POST['newPsw']) == 0) {
            $errors .= '<li>Nuova password non inserita.</li>';
        } else {
            if($_POST['newPsw'] != $_POST['pswCheck']) {
                $errors .= '<li>Le due nuove password non combaciano.</li>';
            } else {
                $newPassword = $_POST['newPsw'];
            }
        }

        if(strlen($errors) == 0) {
            if($conn->openDB()) {
                $checkPsw = $conn->checkNewPassword($username, $oldPassword, $newPassword);

                if(strlen($checkPsw) == 0) {
                    //creo oggetto utente e faccio l'insert con la funzione
                    $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    $newUser = new User($_SESSION['user']->getCF(),
                        $_SESSION['user']->getNome(),
                        $_SESSION['user']->getCognome(),
                        $_SESSION['user']->getNascita(),
                        $_SESSION['user']->getTelefono(),
                        $email,
                        1,
                        $username,
                        $newPassword);

                    if($conn->updateUserPassword($newUser)) {
                        $_SESSION['user'] = $newUser;
                        $errors = '<li>Password aggiornata con successo.</li>';
                    } else {
                        $errors = '<li>Errore durante l\'aggiornamento della password.</li>';
                    }
                } else {
                    $errors = $checkPsw;
                }

                $conn->closeDB();
            } else {
                $globalError = 'Errore di connessione, riprovare più tardi.';
            }
        }
    }

    $nome = strlen($nome) > 0 ? $nome : $_SESSION['user']->getNome();
    $cognome = strlen($cognome) > 0 ? $cognome : $_SESSION['user']->getCognome();
    $nascita = strlen($nascita) > 0 ? $nascita : $_SESSION['user']->getNascita();
    $telefono = strlen($telefono) > 0 ? $telefono : $_SESSION['user']->getTelefono();
    $email = strlen($email) > 0 ? $email : $_SESSION['user']->getEmail();
    $username = strlen($username) > 0 ? $username : $_SESSION['user']->getUserName();

    if(strlen($globalError) > 0)
        $globalError = '<p class=\'error\'>'.$globalError.'</p>';

    if(strlen($errors) > 0)
        $errors = '<ul>'.$errors.'</ul>';

    if(strlen($messaggiForm) > 0)
        $messaggiForm = '<ul>'.$messaggiForm.'</ul>';


    $page = str_replace('<globalError/>', $globalError, $page);

    // PRIMO FORM
    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);

    $page = str_replace('_cognome_', $cognome, $page);
    $page = str_replace('_nome_', $nome, $page);
    $page = str_replace('_nascita_', $nascita, $page);
    $page = str_replace('_telefono_', $telefono, $page);

    // SECONDO FORM
    $page = str_replace('<errors/>', $errors, $page);

    $page = str_replace('_email_', $email, $page);
    $page = str_replace('_username_', $username, $page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;

?>
