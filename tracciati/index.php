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
                <div class="infoTracciato">
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
                </div>
                <img src="" alt="" class="imgTracciati">
            </article>
            */

            if($records !== null) {
                foreach($records as $record) {
                    $recordsBody .= '<article>';
                    $recordsBody .= '<div class="infoTracciato">';
                    $recordsBody .= '<h2>Tracciato <span aria-hidden="true">#</span>'.$record['id'].'</h2>';

                    $recordsBody .= '<p>'.$record['descrizione'].'</p>';

                    $record['terreno'][0] = strtolower($record['terreno'][0]);
                    $record['terreno'] = str_replace('_',' ',$record['terreno']);

                    $recordsBody .= '<ul>';
                    $recordsBody .= '<li>Lunghezza: '.$record['lunghezza'].'<abbr title=\'metri\'>m</abbr></li>';
                    $recordsBody .= '<li>Tipo di terreno: '.$record['terreno'].'</li>';
                    $recordsBody .= '<li>Orario di apertura: <time>'.substr($record['apertura'],0,5).'</time></li>';
                    $recordsBody .= '<li>Orario di chiusura: <time>'.substr($record['chiusura'],0,5).'</time></li>';
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

    if(strlen($globalError) > 0)
        $globalError = "<p class=\"error\">$globalError</p>";

    if(strlen($errorDetails) > 0)
        $errorDetails = "<p class=\"error\">$errorDetails</p>";

    $page = str_replace('<globalError/>',$globalError,$page);
    $page = str_replace('<erroreTracciati/>',$errorDetails,$page);
    $page = str_replace('<dettaglioTracciati/>',$recordsBody,$page);


    echo $page;
?>