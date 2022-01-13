<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;
    use function UTILS\sanitizeInputString;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login/');

    $page = file_get_contents("datiUtente.html");
    $conn = new dbAccess();

    $errors = "";
    $globalError = '';

    $name = $_SESSION['user']->getNome();
    $surname = $_SESSION['user']->getCognome();
    $birth = $_SESSION['user']->getNascita();
    $phone = $_SESSION['user']->getTelefono();
    $email = $_SESSION['user']->getEmail();
    $cf = $_SESSION['user']->getCF();
    $img = $_SESSION['user']->getImgPath();

    echo str_replace("_nome_", $name, $page);
    echo str_replace("_cognome_", $surname, $page);
    echo str_replace("_nascita_", $birth, $page);
    echo str_replace("_telefono_", $phone, $page);
    echo str_replace("_email_", $email, $page);
    echo str_replace("_cf_", $cf, $page);
    echo str_replace("_img_path_", $img, $page);


    if($conn->openDB()) {

        try {
            //





        } catch (Throwable $t) {
            $errors .= $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';


    echo str_replace("<globalError/>", $globalError, $page);

    echo str_replace("<globalError/>", $globalError, $page);
    echo str_replace("<globalError/>", $globalError, $page);

?>
