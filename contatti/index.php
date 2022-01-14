    <?php

    require_once('../utils/db.php');
    require_once('../utils/utils.php');
    require_once('../utils/message.php');

    use DB\dbAccess;
    use MESSAGGIO\Message;
    use function UTILS\sanitizeInputString;
    use function UTILS\checkInputValidity;

    $page = file_get_contents('contatti.html');

    $conn = new dbAccess();

    $globalError = '';
    $messaggiForm = '';

    $nome = '';
    $cognome = '';
    $email = '';
    $tel = '';
    $obj = '';
    $text = '';
    $checked = '';


    if (isset($_POST['submit'])) {
        $nome = sanitizeInputString($_POST['nome']);
        switch (checkInputValidity($nome, '/\D/')) {
            case 1:
                $messaggiForm .= '<li>Nome non presente.</li>';
                break;
            case 2:
                $messaggiForm .= '<li>Nome non può contenere numeri.</li>';
                break;
            default:
                break;
        }

        $cognome = sanitizeInputString($_POST['cognome']);
        switch (checkInputValidity($cognome, '/\D/')) {
            case 1:
                $messaggiForm .= '<li>Cognome non presente.</li>';
                break;
            case 2:
                $messaggiForm .= '<li>Cognome non può contenere numeri.</li>';
                break;
            default:
                break;
        }

        $email = sanitizeInputString($_POST['email']);
        switch (checkInputValidity($cognome,null)) {
            case 1:
                $messaggiForm .= '<li>Email non presente.</li>';
                break;
            default:
                break;
        }

        if(strlen($email) > 0 && !filter_var($email,FILTER_VALIDATE_EMAIL))
            $messaggiForm .= '<li>Email non valida.</li>';

        $tel = sanitizeInputString($_POST['telefono']);
        switch (checkInputValidity($tel,'/\d/')) {
                case 1:
                    $messaggiForm .= '<li>Numero di telefono non presente.</li>';
                    break;
                case 2:
                    $messaggiForm .= '<li>Il numero di telefono non può contenere caratteri diversi da numeri.</li>';
                    break;
                default:
                    break;
        }

        $obj = sanitizeInputString($_POST['oggetto']);
        switch (checkInputValidity($obj)) {
            case 1:
                $messaggiForm .= '<li>Oggetto del messaggio non presente.</li>';
                break;
            default:
                break;
        }

        $text = sanitizeInputString($_POST['messaggio']);
        switch (checkInputValidity($obj)) {
            case 1:
                $messaggiForm .= '<li>Testo del messaggio non presente.</li>';
                break;
            default:
                break;
        }

        if(!isset($_POST['termini']))
            $messaggiForm .= '<li>Devi accettare i termini di servizio e l\'informativa sulla <span lang="en">privacy.</li>';
        else
            $checked = 'checked';

        if ($messaggiForm == '') {
            if ($conn->openDB()) {
                $nome[0] = strtoupper($nome[0]);
                $cognome[0] = strtoupper($cognome);

                $messaggio = new Message(-1,($nome.' '.$cognome),$email,$tel,$obj,$text);
                $newId = $conn->createMessage($messaggio);

                if ($newId > -1) {
                    $messaggiForm = 'Messaggio inviato correttamente.'; //dovrebbe inviare mail ma non possiamo farlo :( )

                    $text = '';
                    $tel = '';
                    $nome = '';
                    $cognome = '';
                    $email = '';
                    $checked = '';
                    $obj = '';
                } else
                    $messaggiForm = 'Errore durante l\'invio del messaggio.';

                $conn->closeDB();
            } else {
                $globalError = 'Errore di connessione, riprovare più tardi.';
            }
        } else {
            $messaggiForm = '<ul>' . $messaggiForm . '</ul>';
        }
    }

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);
    $page = str_replace('<globalError/>', $globalError, $page);

    $page = str_replace('_nome_', $nome, $page);
    $page = str_replace('_cognome_', $cognome, $page);
    $page = str_replace('_email_', $email, $page);
    $page = str_replace('_tel_', $tel, $page);
    $page = str_replace('_obj_', $obj, $page);
    $page = str_replace('_text_', $text, $page);
    $page = str_replace('_checked_', $checked, $page);

    echo $page;
?>