<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login/');


    $page = file_get_contents('tracciati.html');

    $conn = new dbAccess();

    $globalError = '';
    $errorTracciati = '';
    $recordsBody = '';
    $table = '';

    if ($conn->openDB()) {
        //get tracks infos
        try {
            $tracks = $conn->getQueryResult(dbAccess::QUERIES[4]);

            if($tracks !== null) {
                $table = '<table title="tabella contenente le informazioni dei tracciati">
                                <caption>Informazioni sui tracciati presenti nell\'impianto</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Lunghezza</th>
                                        <th scope="col">Terreno</th>
                                        <th scope="col">Orario apertura</th>
                                        <th scope="col">Orario chiusura</th>
                                        <th  scope="col">Modifica</th>
                                        <th  scope="col">Elimina</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tracciati/>
                                </tbody>
                            </table>';

                foreach($tracks as $track) {
                    $track['terreno'][0] = strtoupper($track['terreno'][0]);
                    $track['terreno'] = str_replace('_',' ',$track['terreno']);

                    $track['apertura'] = substr($track['apertura'],0,5);
                    $track['chiusura'] = substr($track['chiusura'],0,5);

                    $recordsBody .= '<tr>';
                    $recordsBody .= '<th data-title=\'ID\' scope=\'row\'><span aria-hidden=\'true\'>#</span>'.$track['id'].'</th>';
                    $recordsBody .= '<td data-title=\'lunghezza\'>'.$track['lunghezza'].'<abbr title=\'metri\'>m</abbr></td>';
                    $recordsBody .= '<td data-title=\'terreno\'>'.$track['terreno'].'</td>';
                    $recordsBody .= '<td data-title=\'orario apertura\'><time>'.$track['apertura'].'</time></td>';
                    $recordsBody .= '<td data-title=\'orario chiusura\'><time>'.$track['chiusura'].'</time></td>';
                    $recordsBody .= '<td data-title=\'modifica\'><a href=\'gestioneTracciato.php?id='.$track['id'].'\' aria-label=\'modifica tracciato\'><i class=\'fas fa-pen\'></i></a></td>';
                    $recordsBody .= '<td data-title=\'elimina\'><a href=\'deleteTracciato.php?id='.$track['id'].'\' aria-label=\'elimina tracciato\'><i class=\'fas fa-trash\'></i></a></td>';
                    $recordsBody .= '</tr>';
                }
            } else {
                $errorTracciati = 'Non è ancora stato inserito alcun tracciato.';
            }
        } catch (Throwable $t) {
            $errorTracciati = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    if(strlen($globalError) > 0)
        $globalError = "<p class='error'>$globalError</p>";

    if(strlen($errorTracciati) > 0)
        $errorTracciati = "<p class='error'>$errorTracciati</p>";

    $page = str_replace('_tracciati_',$table,$page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreTracciati/>',$errorTracciati,$page);
    $page = str_replace('<tracciati/>',$recordsBody,$page);

    $page = str_replace('_userIcon_',strtolower($_SESSION['user']->getNome()[0]),$page);

    echo $page;
?>