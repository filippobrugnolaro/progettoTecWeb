<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');
    require_once('../../utils/utils.php');
    require_once('../../utils/lessonReservation.php');

    use DB\dbAccess;
    use PRENOTAZIONELEZ\LessonReservation;
    use function UTILS\checkInputValidity;
    use function UTILS\sanitizeInputString;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login/');


    $page = file_get_contents('corsi.html');

    $conn = new dbAccess();

    $globalError = '';

    $prenotazioni = '';
    $errorPrenotazione = '';

    $corsiBody = '';
    $errorCorsi = '';

    $corsiDropdown = '';

    $cfUtente = $_SESSION['user']->getCF();

    $corsoScelto = '';
    $noleggioMoto = '';
    $moto = '';
    $noleggioAttrezzatura = '';

    $messaggiForm = '';

    $form = '';

    $error = false;

    $tableIngressiPrenotati = '';
    $tableIngressiDisp = '';

    if(isset($_POST['submit'])) {
        $corsoScelto = $_POST['corso'];
        switch(checkInputValidity($corsoScelto)) {
            case 1: $messaggiForm .= '<li>Corso non presente.</li>'; break;
            default: break;
        }

        if(isset($_POST['moto'])) {
            $noleggioMoto = 'checked';
            $moto = $_POST['motoNoleggio'];
        } else
            $moto = null;

        if(isset($_POST['vestiario']))
            $noleggioAttrezzatura = 'checked';

        if(strlen($messaggiForm) == 0) {
            if($conn->openDB()) {
                $newCorso = new LessonReservation($_SESSION['user']->getCF(),$corsoScelto,strlen($noleggioMoto) > 0 ? 1 : 0, $moto, strlen($noleggioAttrezzatura) > 0 ? 1 : 0);

                $res = $conn->createLessonReservation($newCorso);

                if($res == 0) {
                    $messaggiForm = '<li>Prenotazione corso inserita con successo.</li>';
                    $date = '';
                    $noleggioMoto = '';
                    $noleggioAttrezzatura = '';
                    $moto = '';
                }
                else if ($res == -1) {
                    $messaggiForm = '<li>Impossibile prenotare corso. Hai già prenotato un ingresso per questa data!</li>';
                    $error = true;
                } else {
                    $messaggiForm = '<li>Errore durante l\'inserimento della prenotazione del corso.</li>';
                    $error = true;
                }

                $conn->closeDB();
            } else {
                $globalError = 'Errore di connessione, riprovare più tardi.';
            }
            try {

            } catch(Throwable $t) {
                $messaggiForm = '<li>'.$t->getMessage().'</li>';
            }
        }
    }

    if ($conn->openDB()) {
        //get next n lesson reservations
        try {
            $corsi = $conn->getSpecificQueryResult(str_replace('_cfUser_', $cfUtente, dbAccess::QUERIES[15][0]), dbAccess::QUERIES[15][1]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($corsi !== null) {
                $tableIngressiPrenotati = '<table title="tabella contenente i prossimi corsi prenotati">
                                                <caption>Corsi prenotati</caption>
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Corso</th>
                                                        <th scope="col">Data</th>
                                                        <th scope="col">Istruttore</th>
                                                        <th scope="col">Tracciato</th>
                                                        <th scope="col">Moto</th>
                                                        <th scope="col">Attrezzatura</th>
                                                        <th scope="col">Elimina</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <nextLezioni/>
                                                </tbody>
                                            </table>';

                foreach($corsi as $corso) {
                    $dw = $weekDays[date('w',strtotime($corso['data']))];

                    if($corso['marca'] != null) {
                        $moto = $corso['marca'].' '.$corso['modello'].' '.$corso['anno'];
                    } else
                        $moto = 'Propria';

                    if($corso['attrezzatura'] != 0)
                        $attrezzatura = 'Da noleggiare';
                    else
                        $attrezzatura = 'Propria';

                    $prenotazioni .= '<tr>';
                    $prenotazioni .= '<td>Corso #'.$corso['id'].'</td>';
                    $prenotazioni .= '<td scope=\'row\'>'.$dw.' '.date('d/m/Y',strtotime($corso['data'])).'</td>';
                    $prenotazioni .= '<td>'.$corso['istruttore'].'</td>';
                    $prenotazioni .= '<td>#'.$corso['pista'].'</td>';
                    $prenotazioni .= '<td>'.$moto.'</td>';
                    $prenotazioni .= '<td>'.$attrezzatura.'</td>';
                    $prenotazioni .= '<td><a href=\'deletePrenotazione.php?id='.$corso['codice'].'\' aria-label=\'elimina ingresso\'><i class=\'fas fa-trash\'></i></a></td>';
                    $prenotazioni .= '</tr>';
                }
            } else {
                $errorPrenotazione = 'Non hai nessun corso prenotato. Prenotane uno!';
            }
        } catch (Throwable $t) {
            $errorPrenotazione = $t->getMessage();
        }

        //get next n dates
        try {
            $corsi = $conn->getSpecificQueryResult(str_replace('_cfUser_',$cfUtente,dbAccess::QUERIES[22][0]),dbAccess::QUERIES[22][1]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($corsi !== null) {
                $tableIngressiDisp = '<table title="tabella contenente i prossimi corsi prenotati disponibili">
                                            <caption>Prossimi corsi</caption>
                                            <thead>
                                                <tr>
                                                    <th scope="col">Corso</th>
                                                    <th scope="col">Data</th>
                                                    <th scope="col">Giorno</th>
                                                    <th scope="col">Istruttore</th>
                                                    <th scope="col">Tracciato</th>
                                                    <th scope="col">Posti disponibili</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <nextDate/>
                                            </tbody>
                                        </table>';

                foreach($corsi as $corso) {
                    $dw = $weekDays[date('w',strtotime($corso['data']))];

                    $corsiBody .= '<tr>';
                    $corsiBody .= '<td scope=\'row\'>Corso #'.$corso['id'].'</td>';
                    $corsiBody .= '<td scope=\'row\'>'.date('d/m/Y',strtotime($corso['data'])).'</td>';
                    $corsiBody .= '<td>'.$dw.'</td>';
                    $corsiBody .= '<td>'.$corso['istruttore'].'</td>';
                    $corsiBody .= '<td>#'.$corso['pista'].'</td>';
                    $corsiBody .= '<td>'.($corso['posti'] - $corso['occupati']).'</td>';
                    $corsiBody .= '</tr>';

                    if($corsoScelto == $corso['id'])
                        $corsiDropdown .= "<option value=\"".$corso['id']."\" selected>#".$corso['id'].", ".date('d/m/Y',strtotime($corso['data']))."</option>";
                    else
                        $corsiDropdown .= "<option value=\"".$corso['id']."\">#".$corso['id'].", ".date('d/m/Y',strtotime($corso['data']))."</option>";
                }
            } else {
                $errorCorsi = 'Non ci sono corsi prenotabili disponibili.';
            }

        } catch (Throwable $t) {
            $errorCorsi .= $t->getMessage();
        }
        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($corsiDropdown) > 0 || $error)
        $form = '<form method="post" action="./">
                    <messaggiForm/>

                    <label for="corso">Corso*</label>
                    <select id="corso" name="corso" required>
                        <corsoDisp/>
                    </select>
                    <br>

                    <label for="descCorso">Descrizione del corso:</label>
                    <p id="descCorso"></p>

                    <fieldset>
                        <legend>Noleggio</legend>

                        <p id="hint"></p>

                        <label for="moto">Noleggio moto</label>
                        <input type="checkbox" id="moto" name="moto" value="moto">

                        <label for="motoNol">Tipo di moto</label>
                        <select id="motoNol" name="motoNoleggio">
                        </select>
                        <br/>

                        <label for="vestiario">Attrezzatura</label>
                        <input type="checkbox" id="vestiario" name="vestiario" value="vestiario">
                    </fieldset>

                    <input type="submit" name="submit" value="PRENOTA">
                </form>';
    else
        $form = '<p class=\'error\'>Form prenotazione corsi non disponibile.</p>';


    if(strlen($globalError) > 0)
        $globalError = '<p>'.$globalError.'</p>';

    if(strlen($messaggiForm) > 0)
        $messaggiForm = '<ul>'.$messaggiForm.'</ul>';

    if(strlen($errorCorsi) > 0)
        $errorCorsi = '<p>'.$errorCorsi.'</p>';

    if(strlen($errorPrenotazione) > 0)
        $errorPrenotazione = '<p>'.$errorPrenotazione.'</p>';

    $page = str_replace('_prenotati_',$tableIngressiPrenotati,$page);
    $page = str_replace('_calendario_',$tableIngressiDisp,$page);
    $page = str_replace('_form_',$form,$page);

    $page = str_replace('<globalError/>',$globalError,$page);

    $page = str_replace('<nextLezioni/>',$prenotazioni,$page);
    $page = str_replace('<erroreLezione/>', $errorPrenotazione, $page);

    $page = str_replace('<nextDate/>',$corsiBody,$page);
    $page = str_replace('<erroreNextDate/>', $errorCorsi, $page);

    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);


    $page = str_replace('<corsoDisp/>',$corsiDropdown,$page);
    $page = str_replace('_checkedAttr_',$noleggioAttrezzatura,$page);
    $page = str_replace('_checkedMoto_',$noleggioMoto,$page);

    echo $page;
?>
