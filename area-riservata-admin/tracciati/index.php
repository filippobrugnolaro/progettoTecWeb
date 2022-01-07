<?php
    require_once('../../utils/db.php');
    require_once('../../utils/user.php');

    use DB\dbAccess;

    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']->getTipoUtente() != 2)
        header('Location: ../../login.php');


    $page = file_get_contents('tracciati.html');

    $conn = new dbAccess();

    $globalError = '';
    $errorTracciati = '';
    $recordsBody = '';

    if ($conn->openDB()) {
        //get tracks infos
        try {
            $tracks = $conn->getQueryResult(dbAccess::QUERIES[4]);

            if($tracks !== null)
                foreach($tracks as $track) {
                    $track['terreno'][0] = strtoupper($track['terreno'][0]);
                    $track['terreno'] = str_replace('_',' ',$track['terreno']);

                    $track['apertura'] = substr($track['apertura'],0,5);
                    $track['chiusura'] = substr($track['chiusura'],0,5);

                    $recordsBody .= '<tr>';
                    $recordsBody .= '<td scope=\'col\'>#'.$track['id'].'</td>';
                    $recordsBody .= '<td>'.$track['lunghezza'].'<abbr title=\'metri\'>m</abbr></td>';
                    $recordsBody .= '<td>'.$track['terreno'].'</td>';
                    $recordsBody .= '<td>'.$track['apertura'].'</td>';
                    $recordsBody .= '<td>'.$track['chiusura'].'</td>';
                    $recordsBody .= '<td><a href=\'gestioneTracciato.php?id='.$track['id'].'\' aria-label=\'modifica tracciato\'><i class=\'fas fa-pen\'></i></a></td>';
                    $recordsBody .= '<td><a href=\'deleteTracciato.php?id='.$track['id'].'\' aria-label=\'elimina tracciato\'><i class=\'fas fa-trash\'></i></a></td>';
                    $recordsBody .= '</tr>';
                }
        } catch (Throwable $t) {
            $errorTracciati = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('img_path', '../'.$_SESSION['user']->getImgPath(), $page);
    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreTracciati/>',$errorTracciati,$page);
    $page = str_replace('<tracciati/>',$recordsBody,$page);

    echo $page;
?>