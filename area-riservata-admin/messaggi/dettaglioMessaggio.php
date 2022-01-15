<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');

    if(!isset($_GET['id']))
        header('Location: ./');

    $id = $_GET['id'];

    $page = file_get_contents('dettaglioMessaggio.html');
    $globalError = '';
    $errorDetails = '';
    $recordsBody = '';

    $nominativo = '';
    $email = '';
    $tel = '';
    $obj= '';
    $text = '';

    $messaggioBody = '';

    $conn = new dbAccess();

    if($conn->openDB()) {
        try {
            $records = $conn->getSpecificQueryResult(str_replace('_id_',$id,dbAccess::QUERIES[19][0]),dbAccess::QUERIES[19][1]);

            if($records !== null) {
                $messaggioBody = '<article>
                                        <ul>
                                            <li>Nominativo: _nominativo_</li>
                                            <li>Email: <a href="mailto:_email_?subject=RE: _obj_">_email_</a></li>
                                            <li>Telefono: <a href="tel:_tel_">_tel_</a></li>
                                        </ul>

                                        <label for="oggetto">Oggetto:</label>
                                        <p id="oggetto">_obj_</p>

                                        <label for="messaggio">Messaggio:</label>
                                        <p id="messaggio">_messaggio_</p>
                                    </article>';

                $record = $records[0];
                unset($records);

                $nominativo = $record['nominativo'];
                $email = $record['email'];
                $tel = $record['telefono'];
                $obj = $record['oggetto'];
                $text = $record['testo'];

            } else {
                $errorDetails = 'Non è stato possibile recuperare il messaggio.';
            }
        } catch (Throwable $t) {
            $errorDetails = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($globalError) > 0)
        $globalError = "<p class='error'>$globalError</p>";

    if(strlen($errorDetails) > 0)
        $errorDetails = "<p class='error'>$errorDetails</p>";

    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreMessaggio/>',$errorDetails,$page);

    $page = str_replace('_messaggioBody_',$messaggioBody,$page);
    $page = str_replace('_nominativo_',$nominativo,$page);
    $page = str_replace('_tel_',$tel,$page);
    $page = str_replace('_email_',$email,$page);
    $page = str_replace('_obj_',$obj,$page);
    $page = str_replace('_messaggio_',$text,$page);

    echo $page;
?>