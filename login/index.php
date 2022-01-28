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
        if (strlen($_POST['username']) == 0) {
            $errors .= '<li><span lang="en">Username</span> non inserito.</li>';
        } else if (!preg_match('/^(?=.{4,10}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9]+(?<![_.])$/',$_POST['username'])) {
            $errors .= '<li><span lang="en">Username</span> inserito non valido.</li>';
        } else {
            $username = sanitizeInputString($_POST['username']);
        }

        if (strlen($_POST['password']) == 0) {
            $errors .= '<li>Password non inserita.</li>';
        } else
            $password = $_POST['password'];

        if (strlen($errors) == 0) {
            try {
                if ($conn->openDB()) {
                    $user = $conn->searchUser($username, $password);

                    $conn->closeDB();

                    if ($user === null)
                        $errors = '<li>Username o password non corretti.</li>';
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