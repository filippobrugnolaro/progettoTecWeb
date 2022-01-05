<?php
    require_once("utils/db.php");
    require_once("utils/utils.php");

    use DB\dbAccess;
    use function UTILS\sanitizeInputString;

    $page = file_get_contents("login.html");
    $conn = new dbAccess();

    $errors = "";

    if(isset($_POST["submit"])) {
        if(strlen($_POST["email"]) == 0) {
            $errors .= "<li>Email non inserita</li>";
        } else if(filter_var($_POST["email"],FILTER_VALIDATE_EMAIL) == false) {
            $errors .= "<li>Email inserita non valida.</li>";
        } else {
            $email = sanitizeInputString($_POST["email"]);
        }

        if(strlen($_POST["password"]) == 0) {
            $errors .= "<li>Password non inserita</li>";
        } else {
            $password = sanitizeInputString($_POST["password"]);
        }

        if(strlen($errors) == 0) {
            try {
                $user = $conn->search_user($email,$password);
            } catch (Exception $e) {
                $errors = $e->getMessage();
            }
        } else {
            $errors = "<ul>$errors</ul>";
        }
    }

    $page = str_replace("<messaggiForm/>",$errors,$page);

    echo $page;
?>