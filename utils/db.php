<?php
	namespace DB;

	require_once('moto.php');
	require_once('user.php');
	require_once('track.php');
	require_once('entry.php');
	require_once('lesson.php');
	require_once('lessonReservation.php');

	use Exception;
	use MOTO\DirtBike;
	use TRACCIATO\Track;
	use USER\User;
	use INGRESSO\Entry;
	use LEZIONE\Lesson;
	use PRENOTAZIONELEZ\LessonReservation;
	use MESSAGGIO\Message;
use mysqli;
use PRENOTAZIONE\Reservation;
	use Throwable;

	class dbAccess {
		private const HOST = '127.0.0.1';
		private const NAME = 'acavalie';
		private const USER = 'acavalie';
		private const PSW = 'aexie7Aht6aut3uo';

		public const QUERIES = array(
			//0
			array('SELECT numero, marca, modello, cilindrata, anno, COUNT(*) AS disponibili
				FROM moto
				GROUP BY marca, modello, cilindrata, anno
				ORDER BY numero',
				'Errore durante il recupero delle informazioni sulle moto.'), //get all dirtbikes
			//1
			array('SELECT * FROM moto WHERE numero = _num_',
				'Errore durante il recupero delle informazioni sulla moto.'), //get specific dirtbike
			//2
			array('SELECT data, COUNT(*) AS totNoleggi FROM noleggio WHERE data >= CURDATE() GROUP BY data ORDER BY data',
				'Errore durante il recupero delle informazioni sui noleggi'), //get rent infos
			//3
			array('SELECT cognome, nome, marca, modello, numero, attrezzatura, anno FROM (noleggio INNER JOIN utente ON noleggio.utente = utente.cf) '
				.'INNER JOIN moto ON noleggio.moto = moto.numero WHERE data = \'_data_\'',
				'Errore durante il recupero delle informazioni sul noleggio per la data scelta'), //get rent details for a specific date
			//4
			array('SELECT * FROM pista','Errore durante il recupero delle informazioni dei tracciati'), //get all tracks
			//5
			array('SELECT * FROM pista WHERE id = _id_','Errore durante il recupero delle informazioni sul tracciato'), // get specific track
			//6
			array('SELECT data_disponibile.data AS data, posti,
					(SELECT COUNT(*) FROM ingressi_entrata WHERE ingressi_entrata.data = data_disponibile.data
					GROUP BY ingressi_entrata.data) AS occupati
				FROM data_disponibile
				WHERE data_disponibile.data >= CURDATE()
				HAVING occupati IS NOT NULL
				ORDER BY data_disponibile.data',
				'Errore durante il recupero delle informazioni sugli ingressi'), //get future entries

				//7
			array('SELECT * FROM data_disponibile WHERE data >= CURDATE()','Errore durante il recupero delle informazioni sulle date d\'apertura'), //get future open days

			//8
			array('SELECT nome, cognome, moto, attrezzatura, marca, modello, anno FROM ((ingressi_entrata INNER JOIN utente ON ingressi_entrata.utente = utente.cf)
				LEFT JOIN noleggio ON ingressi_entrata.utente = noleggio.utente AND ingressi_entrata.data = noleggio.data)
				LEFT JOIN moto ON noleggio.moto = moto.numero
				WHERE ingressi_entrata.data = \'_data_\'',
				'Errore durante il recupero delle informazioni sulla data d\'apertura selezionata'), //get entries info from open date
			//9
			array('SELECT posti FROM data_disponibile WHERE data = \'_data_\'',
				'Errore durante il recupero delle informazioni sulla data d\'apertura selezionata'), //get entry's infos
			//10
			array('SELECT  id, data, posti,
				(SELECT COUNT(*) FROM ingressi_lezione WHERE  ingressi_lezione.lezione = lezione.id
				GROUP BY lezione.data) AS occupati
				FROM  lezione
				WHERE data >= CURDATE()
				HAVING occupati IS NOT NULL
				ORDER BY data',
				'Errore durante il recupero delle informazioni sulle lezioni prenotate'), //get booked lessons' info
			//11
			array('SELECT * FROM lezione WHERE data >= CURDATE() ORDER BY data',
				'Errore durante il recupero delle informazioni sulle lezioni'), //get lessons' info
			//12
			array('SELECT nome, cognome, moto, attrezzatura, marca, modello, anno FROM (((ingressi_lezione INNER JOIN utente ON ingressi_lezione.utente = utente.cf)
				INNER JOIN lezione ON ingressi_lezione.lezione = lezione.id)
				LEFT JOIN noleggio ON ingressi_lezione.utente = noleggio.utente AND lezione.data = noleggio.data)
				LEFT JOIN moto ON moto.numero = noleggio.moto
				WHERE ingressi_lezione.lezione = \'_lezione_\'',
				'Errore durante il recupero delle informazioni sulle prenotazioni del corso selezionato'), //get booked record info from lesson
			//13
			array('SELECT * FROM lezione WHERE id = _lezione_',
				'Errore durante il recupero delle informazioni del corso selezionato'), //get specific lesson info
			//14
			array('SELECT ingressi_entrata.codice AS id, ingressi_entrata.data, marca, modello, anno, attrezzatura FROM
				(ingressi_entrata LEFT JOIN noleggio ON ingressi_entrata.utente = noleggio.utente AND ingressi_entrata.data = noleggio.data)
				LEFT JOIN moto ON noleggio.moto = moto.numero
				WHERE ingressi_entrata.utente = \'_cfUser_\' AND ingressi_entrata.data >= CURDATE()',
				'Errore durante il recupero delle informazioni sulle tue prossime prenotazioni'), //get next n track reservations for a specific user

			//15
			array('SELECT lezione.ID AS id, ingressi_lezione.codice AS codice, lezione.data AS data, istruttore, pista, marca, modello, attrezzatura, anno
				FROM ((ingressi_lezione INNER JOIN lezione ON ingressi_lezione.lezione = lezione.id)
				LEFT JOIN noleggio ON ingressi_lezione.utente = noleggio.utente AND lezione.data = noleggio.data)
				LEFT JOIN moto ON noleggio.moto = moto.numero
				WHERE ingressi_lezione.utente = \'_cfUser_\' AND lezione.data >= CURDATE()',
				'Errore durante il recupero delle informazioni sulle tue prossime lezioni'), //get next n lessons reservations for a specific user

			//16
			array('SELECT  id, data, posti, istruttore, descrizione, pista,
				(SELECT COUNT(*) FROM ingressi_lezione WHERE  ingressi_lezione.lezione = lezione.id
				GROUP BY lezione.data) AS occupati
				FROM  lezione
				WHERE data >= CURDATE()
				ORDER BY data',
			'Errore durante il recupero delle informazioni sulle lezioni prenotate.'), //get complete booked lessons' info

			//17
			array('SELECT data_disponibile.data AS data, posti, COUNT(*) AS occupati FROM data_disponibile LEFT JOIN ingressi_entrata ON
			ingressi_entrata.data = data_disponibile.data WHERE data_disponibile.data >= CURDATE() GROUP BY data_disponibile.data,
			posti ORDER BY data_disponibile.data','Errore durante il recupero delle informazioni sugli ingressi'), //get future entries

			//18
			array('SELECT * FROM messaggio ORDER BY data DESC','Errore durante il recupero dei messaggi'), //get user messages

			//19
			array('SELECT * FROM messaggio WHERE id = _id_','Errore durante il recupero del messaggio'), //get specific user message

			//20
			array('SELECT data_disponibile.data AS data, posti,
					(SELECT COUNT(*) FROM ingressi_entrata WHERE ingressi_entrata.data = data_disponibile.data
					GROUP BY data_disponibile.data) AS occupati
				FROM data_disponibile
				WHERE data_disponibile.data >= CURDATE()
				AND data NOT IN (SELECT data FROM ingressi_entrata WHERE utente = \'_cf_\')
				ORDER BY data_disponibile.data',
				'Errore durante il recupero delle informazioni sugli ingressi prenotati'),

			//21
			array('SELECT * FROM moto
				WHERE numero NOT IN (SELECT moto FROM noleggio WHERE data = \'_date_\')',
				'Errore durante il recupero delle informazioni sugli ingressi'), //get available dirtbikes

			//22
			array('SELECT id,istruttore, data, pista, posti,
				(SELECT COUNT(*) FROM ingressi_lezione WHERE ingressi_lezione.lezione = lezione.id
				GROUP BY lezione.id) AS occupati
				FROM lezione
				WHERE data >= CURDATE()
					AND id NOT IN (SELECT lezione FROM ingressi_lezione WHERE utente = \'_cfUser_\')
				GROUP BY id
				ORDER BY data',
				'Errore durante il recupero delle informazioni sui corsi prenotati'),

			//23
			array('SELECT cf, nome, cognome, nascita
			FROM utente
			WHERE ruolo = 1
			ORDER BY cognome, nome',
			'Errore durante il recupero delle informazioni sugli utenti promuovibli'),

			//24
			array('SELECT data_disponibile.data AS data, posti,
					(SELECT COUNT(*) FROM ingressi_entrata WHERE ingressi_entrata.data = data_disponibile.data
					GROUP BY data_disponibile.data) AS occupati
				FROM data_disponibile
				WHERE data_disponibile.data >= CURDATE()
				ORDER BY data_disponibile.data',
				'Errore durante il recupero delle informazioni sugli ingressi prenotati'),
		);

		private $conn;

		public function openDB(): bool {
			$this->conn = mysqli_connect(dbAccess::HOST, dbAccess::USER, dbAccess::PSW, dbAccess::NAME);

			if(mysqli_connect_errno())
				return false;
			else
				return true;
		}

		public function closeDB() {
			mysqli_close($this->conn);
		}

		/* ***************************** LOGIN ************************** */
		public function searchUser(string $username, string $password): ?User {
			$username = mysqli_real_escape_string($this->conn,$username);

			$sql = "SELECT * FROM utente WHERE username = '$username'";

			$query = mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) {
				if(mysqli_num_rows($query) > 0) {
					$row = mysqli_fetch_assoc($query);

					if(password_verify($password,$row['password'])) {
						$user = new User($row['cf'],$row['nome'],$row['cognome'],$row['nascita'],$row['telefono'],$row['email'],$row['ruolo'],$username);
						mysqli_free_result($query);

						return $user;
					} else
						return null;
				} else
					return null;
			} else
				throw new Exception('Errore durante l\'autenticazione. Riprovare più tardi.');
		}

		/* ***************************** GENERIC ************************** */
		public function getQueryResult(array $set) {
			$query = mysqli_query($this->conn,$set[0]);

			if(!mysqli_error($this->conn)) {
				if(mysqli_num_rows($query)) {
					$result = array();

					while($row = mysqli_fetch_assoc($query))
						array_push($result,$row);

					foreach($result as $row) {
						foreach($row as $key)
							if(is_string($key))
								$key = htmlspecialchars($key);
					}

					mysqli_free_result($query);
					return $result;
				} else
					return null;
			} else
				throw new Exception($set[1]);
		}

		public function getSpecificQueryResult(string $query, string $error) {
			return $this->getQueryResult(array($query,$error));
		}

		/* ***************************** DIRTBIKES MANAGEMENT ************************** */
		public function updateDirtBike(DirtBike $moto): bool {
			$marca = mysqli_real_escape_string($this->conn,$moto->getMarca());
			$marca = strtoupper($marca);

			$modello = mysqli_real_escape_string($this->conn,$moto->getModello());
			$modello = strtoupper($modello);

			$anno = $moto->getAnno();
			$id = $moto->getID();

			$sql = "UPDATE moto SET marca = \"$marca\", modello = \"$modello\", ";
			$sql .= "cilindrata = ".$moto->getCilindrata().", anno = $anno WHERE numero = $id";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) //mysqli_affected_rows doesn't work if fields are the same as before!
				return true;
			else
				return false;
		}

		public function createDirtBike(DirtBike $moto): int {
			$marca = mysqli_real_escape_string($this->conn,$moto->getMarca());
			$marca = strtoupper($marca);

			$modello = mysqli_real_escape_string($this->conn,$moto->getModello());
			$modello = strtoupper($modello);

			$anno = $moto->getAnno();
			$cilindrata = $moto->getCilindrata();

			$sql = "INSERT INTO moto (marca,modello,anno,cilindrata) VALUES (\"$marca\",\"$modello\",$anno,$cilindrata)";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}

		public function deleteDirtBike(int $id) {
			$sql = "DELETE FROM moto WHERE numero = $id";
			mysqli_query($this->conn,$sql);
		}

		/* ***************************** TRACKS MANAGEMENT ************************** */
		public function createTrack(Track $track): int {
			$lunghezza = $track->getLun();

			$desc = mysqli_real_escape_string($this->conn,$track->getDesc());
			$desc[0] = strtoupper($desc[0]);

			$terreno = mysqli_real_escape_string($this->conn,$track->getTerreno());
			$apertura = mysqli_real_escape_string($this->conn,$track->getApertura());
			$chiusura = mysqli_real_escape_string($this->conn,$track->getChiusura());

			$sql = "INSERT INTO pista (lunghezza,descrizione,terreno,apertura,chiusura) VALUES ($lunghezza,";
			$sql .= "\"$desc\",\"$terreno\",\"$apertura\",\"$chiusura\")";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}

		public function updateTrack(Track $track): bool {
			$path = mysqli_real_escape_string($this->conn,$track->getImgPath());
			$path = strlen($track->getImgPath()) > 0 ? "\"".$track->getImgPath()."\"" : "NULL";
			$lunghezza = $track->getLun();

			$desc = mysqli_real_escape_string($this->conn,$track->getDesc());
			$desc[0] = strtoupper($desc[0]);

			$terreno = mysqli_real_escape_string($this->conn,$track->getTerreno());
			$apertura = mysqli_real_escape_string($this->conn,$track->getApertura());
			$chiusura = mysqli_real_escape_string($this->conn,$track->getChiusura());

			$id = $track->getID();

			$sql = "UPDATE pista SET lunghezza = $lunghezza, descrizione = \"$desc\", ";
			$sql .= "terreno = \"$terreno\", apertura = \"$apertura\", ";
			$sql .= "chiusura = \"$chiusura\", foto = $path WHERE id = $id";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) //mysqli_affected_rows doesn't work if fields are the same as before!
				return true;
			else
				return false;
		}

		public function deleteTrack(int $id) {
			$sql = "DELETE FROM pista WHERE id = ".$id."";
			mysqli_query($this->conn,$sql);
		}


		/* ***************************** ENTRIES MANAGEMENT ************************** */
		public function deleteEntry(string $date) {
			$date = mysqli_real_escape_string($this->conn,$date);

			$sql = "DELETE FROM data_disponibile WHERE data = \"".$date."\"";
			mysqli_query($this->conn,$sql);
		}

		public function updateEntry(Entry $entry): bool {
			$posti = $entry->getPosti();
			$data = mysqli_real_escape_string($this->conn,$entry->getDate());

			$sql = "UPDATE data_disponibile SET posti = $posti WHERE data = \"$data\"";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) //mysqli_affected_rows doesn't work if fields are the same as before!
				return true;
			else
				return false;
		}

		public function createEntry(Entry $entry): int {
			$posti = $entry->getPosti();
			$data = mysqli_real_escape_string($this->conn,$entry->getDate());

			$sql = "INSERT INTO data_disponibile (data,posti) VALUES (\"$data\",$posti)";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}


		/* ***************************** LESSONS MANAGEMENT ************************** */
		public function deleteLesson(int $id) {
			$sql = "DELETE FROM lezione WHERE id = $id";
			mysqli_query($this->conn,$sql);
		}

		public function updateLesson(Lesson $lesson): bool {
			$data = mysqli_real_escape_string($this->conn,$lesson->getData());

			$desc = mysqli_real_escape_string($this->conn,$lesson->getDesc());
			$desc[0] = strtoupper($desc[0]);

			$istruttore = mysqli_real_escape_string($this->conn,$lesson->getIstruttore());
			$istruttore[0] = strtoupper($istruttore[0]);

			$tracciato = $lesson->getTrack();
			$posti = $lesson->getPosti();
			$id = $lesson->getID();

			$sql = "SELECT id FROM lezione WHERE data = \"$data\" and pista = $tracciato";
			$query = mysqli_query($this->conn, $sql);

			if(mysqli_num_rows($query) > 0) {
				mysqli_free_result($query);
				return false;
			}

			$sql = "UPDATE lezione SET data = \"$data\", posti = $posti, descrizione = \"$desc\", istruttore = \"$istruttore\", ";
			$sql .= "pista = $tracciato WHERE id = $id";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) //mysqli_affected_rows doesn't work if fields are the same as before!
				return true;
			else
			return false;
		}

		public function createLesson(Lesson $lesson): int {
			$data = mysqli_real_escape_string($this->conn,$lesson->getData());

			$desc = mysqli_real_escape_string($this->conn,$lesson->getDesc());
			$desc[0] = strtoupper($desc[0]);

			$istruttore = mysqli_real_escape_string($this->conn,$lesson->getIstruttore());
			$istruttore[0] = strtoupper($istruttore[0]);

			$tracciato = $lesson->getTrack();
			$posti = $lesson->getPosti();

			$sql = "SELECT id FROM lezione WHERE data = \"$data\" and pista = $tracciato";
			$query = mysqli_query($this->conn, $sql);

			if(mysqli_num_rows($query) > 0) {
				mysqli_free_result($query);
				return -2;
			}

			$sql = "INSERT INTO lezione (data,posti,descrizione,istruttore,pista) VALUES (\"$data\",$posti,\"$desc\",\"$istruttore\",$tracciato)";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}

		/* ***************************** MESSAGE MANAGEMENT ************************** */
		public function createMessage(Message $mex): int {
			$nominativo = mysqli_real_escape_string($this->conn,$mex->getNominativo()); //già controllate lettere maiuscole

			$email = mysqli_real_escape_string($this->conn,$mex->getEmail());
			$email = strtolower($email);

			$tel = mysqli_real_escape_string($this->conn,$mex->getTel());
			$obj = mysqli_real_escape_string($this->conn,$mex->getObj());
			$text = mysqli_real_escape_string($this->conn,$mex->getText());

			$sql = "INSERT INTO messaggio (nominativo,email,telefono,oggetto,testo) VALUES ";
			$sql .= "(\"$nominativo\",\"$email\",\"$tel\",\"$obj\",\"$text\")";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
			return -1;
		}

		/* *************************************************************************** */
		/* ************************************ USER ********************************* */
		/* *************************************************************************** */

		public function createNewUser(User $newUser): int {
			$cf = strtoupper(mysqli_real_escape_string($this->conn,$newUser->getCF()));

			$username = mysqli_real_escape_string($this->conn,$newUser->getUserName());

			$cognome = mysqli_real_escape_string($this->conn,$newUser->getCognome());

			$nome = mysqli_real_escape_string($this->conn,$newUser->getNome());

			$nascita = mysqli_real_escape_string($this->conn,$newUser->getNascita());
			$telefono = mysqli_real_escape_string($this->conn,$newUser->getTelefono());

			$email = mysqli_real_escape_string($this->conn,$newUser->getEmail());
			$email = strtolower($email);

			$password = $newUser->getPsw();
			$role = 1;

			$sql = "INSERT INTO utente (cf,cognome,nome,nascita,telefono,email,password,ruolo,username)
					VALUES (\"$cf\",\"$cognome\",\"$nome\",\"$nascita\",\"$telefono\",\"$email\",\"$password\",$role,\"$username\")";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else {
				if(mysqli_errno($this->conn) != 1062)
					return -1;
				else
					return -2;
			}
		}

		/* **************************** USER DATA MANAGEMENT ************************* */

		public function updateUserData(User $user): bool {
			//usato per identificare l'utente
			$cf = strtoupper(mysqli_real_escape_string($this->conn,$user->getCF()));

			$username = mysqli_real_escape_string($this->conn,$user->getUserName());

			//colonne da aggiornare
			$cognome = mysqli_real_escape_string($this->conn,$user->getCognome());

			$nome = mysqli_real_escape_string($this->conn,$user->getNome());

			$nascita = mysqli_real_escape_string($this->conn,$user->getNascita());
			$telefono = mysqli_real_escape_string($this->conn,$user->getTelefono());

			$sql = "UPDATE utente SET cognome=\"$cognome\", nome=\"$nome\", nascita=\"$nascita\", telefono = \"$telefono\", ";
			$sql .= "username = \"$username\" WHERE cf = \"$cf\"";
			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn))
				return true;
			else
				return false;
		}

		public function checkNewPassword(string $username, string $oldPsw, string $newPsw) : string {
			//controllo che email e psw vecchia siano corette

			if($this->searchUser($username, $oldPsw) != null) {
				//controllo che psw vecchia e nuova siano diverse
				if(strcmp($oldPsw, $newPsw) != 0) {
						return '';
				} else {
					return '<li>La vecchia e la nuova password combaciano.</li>';
				}
			} else {
				return '<li>La vecchia password è errata.</li>';
			}
		}

		public function updateUserPassword(User $user): bool {
			//usato per identificare l'utente
			$cf = mysqli_real_escape_string($this->conn,$user->getCF());
			//colonne da aggiornare
			$email = mysqli_real_escape_string($this->conn,$user->getEmail());
			$email = strtolower($email);

			$password = $user->getPsw();

			$sql = "UPDATE utente SET email = \"$email\", password = \"$password\" WHERE cf = \"$cf\"";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn))
				return true;
			else
				return false;
		}

		/* *************************** RESERVATION MANAGEMENT ************************ */
		public function deleteReservation(int $id) {
			try {
				mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
				mysqli_autocommit($this->conn,false);
				mysqli_begin_transaction($this->conn,MYSQLI_TRANS_START_READ_WRITE);

				$sql = "SELECT noleggio.codice AS codice
					FROM ingressi_entrata INNER JOIN noleggio ON ingressi_entrata.utente = noleggio.utente AND ingressi_entrata.data = noleggio.data
					WHERE ingressi_entrata.codice = $id";

				$query = mysqli_query($this->conn,$sql);

				if(!mysqli_error($this->conn)) {
					if(mysqli_num_rows($query)) {
						$row = mysqli_fetch_assoc($query); //sempre e solo un risultato
						$codice = $row['codice'];
						mysqli_free_result($query);

						$sql = "DELETE FROM noleggio WHERE codice = $codice";
						mysqli_query($this->conn,$sql);
					}
				}

				$sql = "DELETE FROM ingressi_entrata WHERE codice = $id";
				mysqli_query($this->conn,$sql);

				mysqli_commit($this->conn);
				mysqli_autocommit($this->conn,true);
			} catch(Throwable $t) {
				mysqli_rollback($this->conn);
			}
		}

		public function createReservation(Reservation $res): int {
			$user = mysqli_real_escape_string($this->conn,$res->getCF());
			$data = mysqli_real_escape_string($this->conn,$res->getData());

			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

			try {
				mysqli_autocommit($this->conn,false);
				mysqli_begin_transaction($this->conn,MYSQLI_TRANS_START_READ_WRITE);

				//controllo che non abbia già impegni in quel giorno
				// $sql = "SELECT codice FROM ingressi_lezione INNER JOIN lezione ON ingressi_lezione.lezione = lezione.id
				// 	WHERE lezione.data = \"$data\" AND utente = \"$user\"";

				// $query = mysqli_query($this->conn,$sql);

				// if(mysqli_num_rows($query) > 0) {//ha già prenotato un corso!!
				// 	mysqli_free_result($query);
				// 	return -1;
				// }

				$sql = "SELECT cf FROM utente
					WHERE (cf IN (SELECT ingressi_entrata.utente
            					FROM ingressi_entrata
            					WHERE data = \"$data\")
      						OR cf IN (SELECT ingressi_lezione.utente
               						FROM (ingressi_lezione INNER JOIN lezione ON lezione.id = ingressi_lezione.lezione)
                     				INNER JOIN data_disponibile ON lezione.data = data_disponibile.data
               						WHERE lezione.data=\"$data\"))
							AND cf = \"$user\"";

				$query = mysqli_query($this->conn, $sql);

				if(mysqli_num_rows($query) > 0) {//ha già prenotato un corso!!
					mysqli_free_result($query);
					return -1;
				}

				//controllo se ci sono posti disponibili
				$sql = "SELECT data, posti, (SELECT COUNT(*)
												FROM ingressi_entrata
												WHERE ingressi_entrata.data = \"$data\" GROUP BY ingressi_entrata.data) AS occupati
						FROM data_disponibile
						WHERE data=\"$data\"";

				//echo $sql;

				$query = mysqli_query($this->conn,$sql);

				if(mysqli_num_rows($query) > 0) {
					$row = mysqli_fetch_assoc($query);
					mysqli_free_result($query);

					if($row['posti'] - $row['occupati'] <= 0)
						return -3;
				}

				$sql = "INSERT INTO ingressi_entrata (data, utente) VALUES (\"$data\",\"$user\")";
				mysqli_query($this->conn,$sql);

				if($res->getMotoBool()) {
					if($res->getAttrezzatura())
						$attrezzatura = 1;
					else
						$attrezzatura = 0;

					$moto = $res->getMoto();

					$sql = "INSERT INTO noleggio (data,attrezzatura,utente,moto) VALUES(\"$data\",$attrezzatura,\"$user\",$moto)";
					mysqli_query($this->conn, $sql);
				}

				mysqli_commit($this->conn);
				mysqli_autocommit($this->conn,true);

				return 0;
			} catch(Throwable $t) {
				mysqli_rollback($this->conn);
				return -2;
			}
		}

		/* *********************** LESSONS RESERVATION MANAGEMENT ******************** */
	public function deleteLessonReservation(int $id) {
		try {
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			mysqli_autocommit($this->conn,false);
			mysqli_begin_transaction($this->conn,MYSQLI_TRANS_START_READ_WRITE);

			$sql = "SELECT noleggio.codice AS codice
				FROM ingressi_lezione INNER JOIN lezione ON ingressi_lezione.lezione = lezione.id INNER JOIN
				noleggio ON ingressi_lezione.utente = noleggio.utente AND lezione.data = noleggio.data
				WHERE ingressi_lezione.codice = $id";

			$query = mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) {
				if(mysqli_num_rows($query)) {
					$row = mysqli_fetch_assoc($query); //sempre e solo un risultato
					$codice = $row['codice'];
					mysqli_free_result($query);

					$sql = "DELETE FROM noleggio WHERE codice = $codice";
					mysqli_query($this->conn,$sql);
				}
			}

			$sql = "DELETE FROM ingressi_lezione WHERE codice = $id";
			mysqli_query($this->conn,$sql);

			mysqli_commit($this->conn);
			mysqli_autocommit($this->conn,true);
		} catch(Throwable $t) {
			mysqli_rollback($this->conn);
		}
	}

	public function createLessonReservation(LessonReservation $res): int {
		$user = mysqli_real_escape_string($this->conn,$res->getCF());
		$lez = $res->getLesson();

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		try {
			mysqli_autocommit($this->conn,false);
			mysqli_begin_transaction($this->conn,MYSQLI_TRANS_START_READ_WRITE);

			//controllo che non abbia già fatto iscrizione ad un ingresso
			$sql = "SELECT data FROM lezione WHERE id = $lez";
			$query = mysqli_query($this->conn,$sql);

			$data = mysqli_fetch_assoc($query)['data'];
			mysqli_free_result($query);

			$sql = "SELECT cf FROM utente
				WHERE (cf IN (SELECT ingressi_entrata.utente
							FROM ingressi_entrata
							WHERE data = \"$data\")
						OR cf IN (SELECT ingressi_lezione.utente
								FROM (ingressi_lezione INNER JOIN lezione ON lezione.id = ingressi_lezione.lezione)
								INNER JOIN data_disponibile ON lezione.data = data_disponibile.data
								WHERE lezione.data=\"$data\"))
						AND cf = \"$user\"";

			$query = mysqli_query($this->conn, $sql);

			if(mysqli_num_rows($query) > 0) {//ha già prenotato qualcosa!!
				mysqli_free_result($query);
				return -1;
			}

			//controllo se ci sono posti disponibili
			$sql = "SELECT posti, (SELECT COUNT(*)
										FROM ingressi_lezione
										WHERE ingressi_lezione.lezione = \"$lez\" GROUP BY ingressi_lezione.lezione) AS occupati
				FROM lezione
				WHERE id=\"$lez\"";

			$query = mysqli_query($this->conn,$sql);

			if(mysqli_num_rows($query) > 0) {
				$row = mysqli_fetch_assoc($query);
				mysqli_free_result($query);

				if($row['posti'] - $row['occupati'] <= 0)
					return -3;
			}

			$sql = "INSERT INTO ingressi_lezione (lezione, utente) VALUES (\"$lez\",\"$user\"); ";
			mysqli_query($this->conn,$sql);

			$sql = "SELECT data FROM lezione WHERE id = $lez";

			$query = mysqli_query($this->conn,$sql);
			$result = mysqli_fetch_assoc($query);

			$data = $result['data'];

			if($res->getMotoBool()) {
				if($res->getAttrezzatura())
					$attrezzatura = 1;
				else
					$attrezzatura = 0;

				$moto = $res->getMoto();

				$sql = "INSERT INTO noleggio (data,attrezzatura,utente,moto) VALUES(\"$data\",$attrezzatura,\"$user\",$moto)";
				mysqli_query($this->conn, $sql);
			}

			mysqli_commit($this->conn);
			mysqli_autocommit($this->conn,true);

			return 0;
		} catch(Throwable $t) {
			mysqli_rollback($this->conn);
			return -2;
		}
	}

	public function updateUserRole(string $cf): bool {
		$cf = mysqli_real_escape_string($this->conn,$cf);

		$sql = "UPDATE utente SET ruolo = 2 WHERE cf = \"$cf\"";

		mysqli_query($this->conn,$sql);

		if(!mysqli_error($this->conn))
			return true;
		else
			return false;
	}
}
?>