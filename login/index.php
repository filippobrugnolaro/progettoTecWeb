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
                $path = '../area-riservata-utente/';
                break;
            case 2:
                $path = '../area-riservata-admin/';
                break;
            default:
                session_destroy(); //should never happen
                break;
        }

        if(isset($_GET['redirect']))
            $path .= $_GET['redirect'].'/';

        header("Location: $path");
    }

    $page = file_get_contents('login.html');
    $conn = new dbAccess();

    $errors = "";

    if(isset($_GET['redirect']))
        $action = './?redirect='.$_GET['redirect'];
    else
        $action = './';

    if (isset($_POST['submit'])) {
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

        if (strlen($errors) == 0) {
            try {
                if ($conn->openDB()) {
                    $user = $conn->searchUser($email, $password);

                    $conn->closeDB();

                    if ($user === null)
                        $errors = '<li>Email o password errata.</li>';
                    else {
                        $_SESSION['user'] = $user;

                        switch(($_SESSION['user'])->getTipoUtente()) {
                            case 1:
                                $path = '../area-riservata-utente/';
                                break;
                            case 2:
                                $path = '../area-riservata-admin/';
                                break;
                            default:
                                session_destroy(); //should never happen
                                break;
                        }

                        if(isset($_GET['redirect']))
                            $path .= $_GET['redirect'].'/';

                        header("Location: $path");

                    }
                } else
                    $errors = '<li>Impossibile effettuare l\'accesso ora, riprovare più tardi.</li>';
            } catch (Throwable $e) {
                $errors .= '<li>'.$e->getMessage().'</li>';
            }
        }
    }

    if(strlen($errors) > 0)
        $errors = "<ul>$errors</ul>";

    $page = str_replace('_action_',$action,$page);
    $page = str_replace('<messaggiForm/>', $errors, $page);

    echo $page;
?>