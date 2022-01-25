<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('corsi.html');

    $conn = new dbAccess();

    $globalError = '';
    $errorCorsi = '';
    $errorCorso = '';
    $recordsBody = '';
    $corsiBody = '';
    $tableCorsi = '';
    $tableCorso = '';

    if ($conn->openDB()) {
        //get booked lessons infos
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[10]);

            if($records !== null) {
                $tableCorsi = '<table title=\'tabella contenente le prenotazioni dei prossimi corsi\'>
				                <caption>Prenotazioni posti per i prossimi corsi</caption>
				                <thead>
					            <tr>
						            <th scope=\'col\'>Corso</th>
						            <th scope=\'col\'>Data</th>
						            <th scope=\'col"\'>Posti disponibili</th>
						            <th scope=\'col\'>Posti rimanenti</th>
						            <th scope=\'col\'>Dettagli</th>
					            </tr>
				                </thead>
				                <tbody>
					            <corsi/>
				            </tbody>
			            </table>';

                foreach($records as $record) {
                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th scope=\'row\'>#'.$record['id'].'</th>';
                    $recordsBody .= '<td>'.date("d/m/Y",strtotime($record['data'])).'</td>'; //controllare accessibilità
                    $recordsBody .= '<td>'.$record['posti'].'</td>';
                    $recordsBody .= '<td>'.($record['posti'] - $record['occupati']).'</td>';
                    $recordsBody .= '<td><a href=\'dettagliCorso.php?id='.$record['id'].'\' aria-label=\'dettaglio corso\'><i class=\'fas fa-info-circle\'></i></a></td>';
                    $recordsBody .= '</tr>';
                }
            } else {
                $errorCorsi = 'Non ci sono ancora prenotazioni per i corsi.';
            }
        } catch (Throwable $t) {
            $errorCorsi = $t->getMessage();
        }

        //get lessons infos
        try {
            $corsi = $conn->getQueryResult(dbAccess::QUERIES[11]);

            if($corsi !== null) {
                $tableCorso = '<table title=\'tabella contenente i prossimi corsi\'>
				                <caption>Prossimi corsi</caption>
				                <thead>
					            <tr>
						            <th scope=\'col\'>Data</th>
						            <th scope=\'col\'>Posti disponibili</th>
						            <th scope=\'col\'>Istruttore</th>
						            <th scope=\'col\'>Tracciato</th>
						            <th scope=\'col\'>Modifica</th>
						            <th scope=\'col\'>Elimina</th>
					            </tr>
				                </thead>
				                <tbody>
					                <corso/>
				                </tbody>
			                </table>';

                foreach($corsi as $corso) {
                    $corsiBody .= '<tr>';
                    $corsiBody .= '<th scope=\'row\'>'.date('d/m/Y',strtotime($corso['data'])).'</th>';
                    $corsiBody .= '<td>'.$corso['posti'].'</td>';
                    $corsiBody .= '<td>'.$corso['istruttore'].'</td>';
                    $corsiBody .= '<td>#'.$corso['pista'].'</td>';
                    $corsiBody .= '<td><a href=\'gestioneCorso.php?id='.$corso['id'].'\' aria-label=\'modifica corso\'><i class=\'fas fa-pen\'></i></a></td>';
                    $corsiBody .= '<td><a href=\'deleteCorso.php?id='.$corso['id'].'\' aria-label=\'elimina corso\'><i class=\'fas fa-trash\'></i></a></td>';
                    $corsiBody .= '</tr>';
                }
            } else {
                $errorCorso = 'Non ci sono corsi previsti.';
            }
        } catch (Throwable $t) {
            $errorCorso = $t->getMessage();
        }
        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($globalError) > 0)
        $globalError = '<p class=\'error\'>'.$globalError.'</p>';

    if(strlen($errorCorso) > 0)
        $errorCorso = '<p class=\'error\'>'.$errorCorso.'</p>';

    if(strlen($errorCorsi) > 0)
        $errorCorsi = '<p class=\'error\'>'.$errorCorsi.'</p>';

    $page = str_replace('_tabellaCorsi_', $tableCorsi, $page);
    $page = str_replace('_tabellaCorso_', $tableCorso, $page);

    $page = str_replace('<erroreCorso/>', $errorCorso, $page);
    $page = str_replace('<erroreCorsi/>', $errorCorsi, $page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<corsi/>',$recordsBody,$page);
    $page = str_replace('<corso/>',$corsiBody,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>
