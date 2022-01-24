<?php
    require_once('../utils/db.php');

    use DB\dbAccess;

    $page = file_get_contents('tracciati.html');

    $conn = new dbAccess();

    $recordsBody = "";
    $globalError = "";
    $errorDetails = "";

    if($conn->openDB()) {
        try {
            $records = $conn->getQueryResult(dbAccess::QUERIES[4]);

            /*
            <article>
                <img src="" alt="" class="imgTracciati">
                <h2>Facile</h2>
                <ul>
                    <li>Lunghezza: 1000 metri</li>
                    <li>Caratteristiche:
                        <ul>
                            <li>curve paraboliche</li>
                            <li>8 salti di bassa altezza e lunghezza</li>
                            <li><span lang="en">waves</span> base</li>
                        </ul>
                    </li>
                    <li>Pubblico consigliato: bambini e piloti con poca esperienza</li>
                </ul>
            </article>
            */

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<article>';
                    $recordsBody .= '<div>';
                    $recordsBody .= '<h2>Tracciato #'.$record['id'].'</h2>';

                    $recordsBody .= '<p>'.$record['descrizione'].'</p>';

                    $record['terreno'][0] = strtoupper($record['terreno'][0]);
                    $record['terreno'] = str_replace('_',' ',$record['terreno']);

                    $recordsBody .= '<ul>';
                    $recordsBody .= '<li>Lunghezza: '.$record['lunghezza'].' metri</li>';
                    $recordsBody .= '<li>Tipo di terreno: '.$record['terreno'].'</li>';
                    $recordsBody .= '<li>Orario di apertura: '.substr($record['apertura'],0,5).'</li>';
                    $recordsBody .= '<li>Orario di chiusura: '.substr($record['chiusura'],0,5).'</li>';
                    $recordsBody .= '</ul>';

                    $recordsBody .= '</div>';

                    if($record['foto'] != null)
                        $recordsBody .= '<img src=\''.('../images/tracks/'.$record['foto']).'\' alt=\'\' class=\'imgTracciati\'>';

                    $recordsBody .= '</article>';
                }
            } else {
                $errorDetails = 'Nessun tracciato ancora presente.';
            }

        } catch (Throwable $t) {
            $errorDetails = $t->getMessage();
        }

        $conn->closeDB();
    } else
        $globalError = 'Errore di connessione, riprovare più tardi.';

    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreTracciati/>',$errorDetails,$page);
    $page = str_replace('<dettaglioTracciati/>',$recordsBody,$page);


    echo $page;
?>