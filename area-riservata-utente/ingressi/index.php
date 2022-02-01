<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');
    require_once('../../utils/utils.php');
    require_once('../../utils/reservation.php');

    use DB\dbAccess;
    use PRENOTAZIONE\Reservation;
    use function UTILS\checkInputValidity;
    use function UTILS\sanitizeInputString;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 1)
        header('Location: ../../login/');


    $page = file_get_contents('ingressi.html');

    $conn = new dbAccess();


    $globalError = '';

    $prenotazioni = '';
    $errorPrenotazione = '';

    $ingressiBody = '';
    $errorIngresso = '';

    $ingressiDropdown = '';

    $cfUtente = $_SESSION['user']->getCF();

    $date = '';
    $noleggioMoto = '';
    $moto = '';
    $noleggioAttrezzatura = '';

    $error = false;

    $messaggiForm = '';

    $tablePrenotazioni = '';
    $tableDisp = '';
    $form = '';

    if(isset($_POST['submit'])) {
        $date = $_POST['dataDisp'];
        switch(checkInputValidity($date,'/^\d{4}-\d{2}-\d{2}$/')) {
            case 1: $messaggiForm .= '<li>Data non presente.</li>'; break;
            case 2: $messaggiForm .= '<li>Formato data non corretto</li>'; break;
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
                $ingresso = new Reservation($_SESSION['user']->getCF(),$date,strlen($noleggioMoto) > 0 ? 1 : 0, $moto, strlen($noleggioAttrezzatura) > 0 ? 1 : 0);

                $res = $conn->createReservation($ingresso);

                if($res == 0) {
                    $messaggiForm = '<li>Prenotazione inserita con successo</li>';
                    $date = '';
                    $noleggioMoto = '';
                    $noleggioAttrezzatura = '';
                    $moto = '';
                }
                else {
                    $error = true;

                    if ($res == -1) $messaggiForm = '<li>Impossibile prenotare ingresso. Hai già un impegno per questa data!</li>';
                    else if($res == -3) $messaggiForm = '<li>Impossibile prenotare ingresso. Non ci sono più posti disponibili per questa data!</li>';
                    else $messaggiForm = '<li>Errore durante l\'inserimento della prenotazione dell\'ingresso.</li>';
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
        //get next n track reservations
        try {
            $ingressi = $conn->getSpecificQueryResult(str_replace('_cfUser_', $cfUtente, dbAccess::QUERIES[14][0]), dbAccess::QUERIES[14][1]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                $tablePrenotazioni = '<table title="tabella contenente i tuoi prossimi ingressi prenotati">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Data</th>
                                                    <th scope="col">Moto</th>
                                                    <th scope="col">Attrezzatura</th>
                                                    <th scope="col">Elimina</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <nextPrenotazioni/>
                                            </tbody>
                                        </table>';

                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    if($ingresso['marca'] != null) {
                        $moto = $ingresso['marca'].' '.$ingresso['modello'].' '.$ingresso['anno'];
                    } else
                        $moto = 'Propria';

                    if($ingresso['attrezzatura'] != 0)
                        $attrezzatura = 'Da noleggiare';
                    else
                        $attrezzatura = 'Propria';

                    $prenotazioni .= '<tr>';
                    $prenotazioni .= '<th data-title=\'data\' scope=\'row\'>'.$dw.' <time datetime=\''.$ingresso['data'].'\'>'.date('d/m/Y',strtotime($ingresso['data'])).'</time></th>';
                    $prenotazioni .= '<td data-title=\'moto\'>'.$moto.'</td>';
                    $prenotazioni .= '<td data-title=\'attrezzatura\'>'.$attrezzatura.'</td>';
                    $prenotazioni .= '<td data-title=\'elimina\'><a href=\'deletePrenotazione.php?id='.$ingresso['id'].'\' aria-label=\'elimina ingresso\'><i class=\'fas fa-trash\'></i></a></td>';
                    $prenotazioni .= '</tr>';
                }
            } else {
                $errorPrenotazione = 'Non hai nessun ingresso prenotato. Prenotane uno!';
            }
        } catch (Throwable $t) {
            $errorPrenotazione = $t->getMessage();
        }

        //get next n dates
        try {
            $ingressi = $conn->getSpecificQueryResult(str_replace('_cf_',$cfUtente,dbAccess::QUERIES[20][0]),dbAccess::QUERIES[20][1]);

            $weekDays = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');

            if($ingressi !== null) {
                $tableDisp = '<div class="tableContainer">
                                <table title="tabella contenente le prossime date di apertura">
                                    <caption>Prossime date di apertura</caption>
                                    <thead>
                                        <tr>
                                            <th scope="col">Data</th>
                                            <th scope="col">Giorno</th>
                                            <th scope="col">Posti disponibili</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <nextDate/>
                                    </tbody>
                                </table>
                            </div>';

                foreach($ingressi as $ingresso) {
                    $dw = $weekDays[date('w',strtotime($ingresso['data']))];

                    $ingressiBody .= '<tr>';
                    $ingressiBody .= '<th data-title=\'data\' scope=\'row\'><time datetime=\''.$ingresso['data'].'\'>'.date('d/m/Y',strtotime($ingresso['data'])).'</time></th>';
                    $ingressiBody .= '<td data-title=\'giorno\'>'.$dw.'</td>';
                    $ingressiBody .= '<td data-title=\'posti disponibili\'>'.($ingresso['posti'] - $ingresso['occupati']).'</td>';
                    $ingressiBody .= '</tr>';

                    if($date == $ingresso['data'])
                        $ingressiDropdown .= "<option value=\"".$ingresso['data']."\" selected>".date('d/m/Y',strtotime($ingresso['data']))."</option>";
                    else
                        $ingressiDropdown .= "<option value=\"".$ingresso['data']."\">".date('d/m/Y',strtotime($ingresso['data']))."</option>";
                }
            } else {
                $errorIngresso = 'Non ci sono date prenotabili disponibili.';
            }

        } catch (Throwable $t) {
            $errorIngresso .= $t->getMessage();
        }
        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';


    if(strlen($globalError) > 0)
        $globalError = '<p>'.$globalError.'</p>';

    if(strlen($messaggiForm) > 0)
        $messaggiForm = '<ul>'.$messaggiForm.'</ul>';

    if(strlen($errorIngresso) > 0)
        $errorIngresso = '<p>'.$errorIngresso.'</p>';

    if(strlen($ingressiDropdown) > 0 || $error)
        $form = '<form method="post" action="./">
                    <messaggiForm/>

                    <div class="input-container">
                        <label for="dataDisponibile">Data*</label>
                        <select id="dataDisponibile" name="dataDisp" aria-required="true">
                            <dataDisp/>
                        </select>
                    </div>

                    <fieldset>
                        <legend>Noleggio</legend>
                        <p id="hint"></p>

                        <div class="input-box">
                            <div class="input-container">
                                <label for="moto">Noleggio moto</label>
                                <input type="checkbox" id="moto" name="moto" value="moto" _checkedMoto_ >
                            </div>

                            <div class="input-container">
                                <label for="motoNol">Tipo di moto</label>
                                <select id="motoNol" name="motoNoleggio">
                                </select>
                            </div>
                        </div>

                        <div class="input-container">
                            <label for="vestiario">Attrezzatura</label>
                            <input type="checkbox" id="vestiario" name="vestiario" value="vestiario" _checkedAttr_>
                        </div>

                        <input type="submit" name="submit" value="PRENOTA">
                    </fieldset>

                </form>';
    else
        $form = '<p class=\'error\'>Form prenotazione ingressi non disponibile.</p>';

    if(strlen($globalError) > 0)
        $globalError = "<p class=\"error\">$globalError</p>";

    if(strlen($errorIngresso) > 0)
        $errorIngresso = "<p class=\"error\">$errorIngresso</p>";

    if(strlen($errorPrenotazione) > 0)
        $errorPrenotazione = "<p class=\"error\">$errorPrenotazione</p>";


    $page = str_replace('_prenotazioni_',$tablePrenotazioni,$page);
    $page = str_replace('_disp_',$tableDisp,$page);
    $page = str_replace('_form_',$form,$page);

    $page = str_replace('<globalError/>',$globalError,$page);

    $page = str_replace('<nextPrenotazioni/>',$prenotazioni,$page);
    $page = str_replace('<errorePrenotazione/>', $errorPrenotazione, $page);

    $page = str_replace('<nextDate/>',$ingressiBody,$page);
    $page = str_replace('<erroreIngresso/>', $errorIngresso, $page);
    $page = str_replace('<messaggiForm/>', $messaggiForm, $page);

    $page = str_replace('<dataDisp/>',$ingressiDropdown,$page);
    $page = str_replace('_checkedAttr_',$noleggioAttrezzatura,$page);
    $page = str_replace('_checkedMoto_',$noleggioMoto,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>
