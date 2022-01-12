<?php
	namespace DB;

	require_once('moto.php');
	require_once('user.php');
	require_once('track.php');
	require_once('entry.php');
	require_once('lesson.php');

	use Exception;
	use MOTO\DirtBike;
	use TRACCIATO\Track;
	use USER\User;
	use INGRESSO\Entry;
	use LEZIONE\Lesson;
	use PRENOTAZIONE\Reservation;

	class dbAccess {
		private const HOST = '127.0.0.1';
		private const NAME = 'acavalie';
		private const USER = 'acavalie';
		private const PSW = 'aexie7Aht6aut3uo';

		public const QUERIES = array(
			//1
			array('SELECT * FROM moto',
				'Errore durante il recupero delle informazioni sulle moto.'), //get all dirtbikes
			//2
			array('SELECT * FROM moto WHERE numero = _num_',
				'Errore durante il recupero delle informazioni sulla moto.'), //get specific dirtbike
			//3
			array('SELECT data, COUNT(*) AS totNoleggi FROM noleggio WHERE data >= CURDATE() GROUP BY data ORDER BY data',
				'Errore durante il recupero delle informazioni sui noleggi'), //get rent infos
			//4
			array('SELECT cognome, nome, marca, modello, numero, attrezzatura FROM (noleggio INNER JOIN utente ON noleggio.utente = utente.cf) '
				.'INNER JOIN moto ON noleggio.moto = moto.numero WHERE data = \'_data_\'',
				'Errore durante il recupero delle informazioni sul noleggio per la data scelta'), //get rent details for a specific date
			//5
			array('SELECT * FROM pista','Errore durante il recupero delle informazioni dei tracciati'), //get all tracks
			//6
			array('SELECT * FROM pista WHERE id = _id_','Errore durante il recupero delle informazioni sul tracciato'), // get specific track
			//7
			array('SELECT data_disponibile.data AS data, posti, COUNT(*) AS occupati FROM ingressi_entrata INNER JOIN data_disponibile ON
				ingressi_entrata.data = data_disponibile.data WHERE data_disponibile.data >= CURDATE() GROUP BY data_disponibile.data,
				posti ORDER BY data_disponibile.data','Errore durante il recupero delle informazioni sugli ingressi'), //get future entries
			//8
			array('SELECT * FROM data_disponibile WHERE data >= CURDATE()','Errore durante il recupero delle informazioni sulle date d\'apertura'), //get future open days
			//9
			array('SELECT nome, cognome, moto, attrezzatura FROM (ingressi_entrata INNER JOIN utente ON ingressi_entrata.utente = utente.cf)
				LEFT JOIN noleggio ON ingressi_entrata.utente = noleggio.utente AND ingressi_entrata.data = noleggio.data
				WHERE ingressi_entrata.data = \'_data_\'',
				'Errore durante il recupero delle informazioni sulla data d\'apertura selezionata'), //get entries info from open date
			//10
			array('SELECT posti FROM data_disponibile WHERE data = \'_data_\'',
				'Errore durante il recupero delle informazioni sulla data d\'apertura selezionata'), //get entry's infos
			//11
			array('SELECT lezione.id AS id, data_disponibile.data AS data, lezione.posti AS posti, COUNT(*) AS occupati FROM
				(ingressi_lezione INNER JOIN lezione ON ingressi_lezione.lezione = lezione.id)
				INNER JOIN data_disponibile ON lezione.data = data_disponibile.data
				WHERE data_disponibile.data >= CURDATE()
				GROUP BY data_disponibile.data, lezione.posti
				ORDER BY data_disponibile.data',
				'Errore durante il recupero delle informazioni sulle lezioni prenotate'), //get booked lessons' info
			//12
			array('SELECT * FROM lezione WHERE data >= CURDATE()',
				'Errore durante il recupero delle informazioni sulle lezioni'), //get lessons' info
			//13
			array('SELECT nome, cognome, moto, attrezzatura FROM ((ingressi_lezione INNER JOIN utente ON ingressi_lezione.utente = utente.cf)
				INNER JOIN lezione ON ingressi_lezione.lezione = lezione.id)
				LEFT JOIN noleggio ON ingressi_lezione.utente = noleggio.utente AND lezione.data = noleggio.data
				WHERE ingressi_lezione.lezione = \'_lezione_\'',
				'Errore durante il recupero delle informazioni sulle prenotazioni del corso selezionato'), //get booked record info from lesson
			//14
			array('SELECT * FROM lezione WHERE id = _lezione_',
				'Errore durante il recupero delle informazioni del corso selezionato'), //get specific lesson info
			//15
			array('SELECT codice AS id, data FROM ingressi_entrata WHERE utente = _cfUser_ AND data >= CURDATE() ORDER BY data LIMIT 3',
				'Errore durante il recupero delle informazioni sulle tue prossime prenotazioni'), //get next n track reservations for a specific user
			//16
			array('SELECT codice AS id, data FROM ingressi_lezione WHERE utente = _cfUser_ AND data >= CURDATE() ORDER BY data LIMIT 3',
				'Errore durante il recupero delle informazioni sulle tue prossime lezioni'), //get next n lessons reservations for a specific user
 
		);

		private $conn;

		public function openDB(): bool {
			$this->conn = mysqli_connect(dbAccess::HOST, dbAccess::USER, dbAccess::PSW, dbAccess::NAME);

			if(mysqli_connect_errno($this->conn))
				return false;
			else
				return true;
		}

		public function closeDB() {
			mysqli_close($this->conn);
		}

		/* ***************************** LOGIN ************************** */
		public function searchUser(string $email, string $password) {
			$email = mysqli_real_escape_string($this->conn,$email);
			$password = mysqli_real_escape_string($this->conn,$password);

			$sql = "SELECT * FROM utente WHERE email = '$email'";

			$query = mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) {
				if(mysqli_num_rows($query) > 0) {
					$row = mysqli_fetch_assoc($query);

					if(password_verify($password,$row['password'])) {
						$user = new User($row['cf'],$row['nome'],$row['cognome'],$row['nascita'],$row['telefono'],$row['email'],$row['ruolo']);
						mysqli_free_result($query);

						$path = "../user-images/".$row['cf'].".jpg";

						if(file_exists($path))
							$user->setImgPath($path);
						else
							$user->setImgPath('../user-images/empty-user.jpg');

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
			$modello = mysqli_real_escape_string($this->conn,$moto->getModello());
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
			$modello = mysqli_real_escape_string($this->conn,$moto->getModello());
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
			$path = $track->getImgPath() != "" ? "\"".$track->getImgPath()."\"" : "NULL";
			$path = mysqli_real_escape_string($this->conn,$path);
			$lunghezza = $track->getLun();
			$desc = mysqli_real_escape_string($this->conn,$track->getDesc());
			$terreno = mysqli_real_escape_string($this->conn,$track->getTerreno());
			$apertura = mysqli_real_escape_string($this->conn,$track->getApertura());
			$chiusura = mysqli_real_escape_string($this->conn,$track->getChiusura());

			$id = $track->getID();

			$sql = "UPDATE pista SET lunghezza = $lunghezza, descrizione = \"$desc\", ";
			$sql .= "terreno = \"$terreno\", apertura = \"$apertura\", ";
			$sql .= "chiusura = \"$chiusura\", foto = \"$path\" WHERE id = $id";

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
			echo $sql;
			mysqli_query($this->conn,$sql);
		}

		public function updateLesson(Lesson $lesson): bool {
			$data = mysqli_real_escape_string($this->conn,$lesson->getData());
			$desc = mysqli_real_escape_string($this->conn,$lesson->getDesc());
			$istruttore = mysqli_real_escape_string($this->conn,$lesson->getIstruttore());
			$tracciato = $lesson->getTrack();
			$posti = $lesson->getPosti();
			$id = $lesson->getID();

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
			$istruttore = mysqli_real_escape_string($this->conn,$lesson->getIstruttore());
			$tracciato = $lesson->getTrack();
			$posti = $lesson->getPosti();

			$sql = "INSERT INTO lezione (data,posti,descrizione,istruttore,pista) VALUES (\"$data\",$posti,\"$desc\",\"$istruttore\",$tracciato)";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
			return -1;
		}


		/* ************************************ USER ********************************* */

		/* **************************** USER DATA MANAGEMENT ************************* */

		/* *************************** RESERVATION MANAGEMENT ************************ */
		public function deleteReservation(int $id) {
			$sql = "DELETE FROM ingressi_entrata WHERE codice = $id";
			echo $sql;
			mysqli_query($this->conn,$sql);

			// bisogna cancellare anche la entry del noleggio 
		}

		public function createReservation(Reservation $res): int {
			$data = mysqli_real_escape_string($this->conn,$res->getDate());
			$user = $_SESSION['user']->getCF();

			//creo reservation su ingressi_entrata
			$sql = "INSERT INTO ingressi_entrata (data, utente) VALUES (\"$data\",$user)";


			if($res->getMoto() || $res->getAttrezzatura()) {
				// va creata anche la entry nella tabella noleggio
				// come andare a prendersi il codice della moto da inserire nella prenotazione ---
			}

			mysqli_query($this->conn, $sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}

		/* *********************** LESSONS RESERVATION MANAGEMENT ******************** */
		public function deleteLessonReservation(int $id) {
			$sql = "DELETE FROM ingressi_lezione WHERE codice = $id";
			echo $sql;
			mysqli_query($this->conn,$sql);

			// bisogna cancellare anche la entry del noleggio 
		}

		public function createLessonReservation(Reservation $res): int {
			$data = mysqli_real_escape_string($this->conn,$res->getDate());
			$user = $_SESSION['user']->getCF();

			//creo reservation su ingressi_entrata
			$sql = "INSERT INTO ingressi_entrata (data, utente) VALUES (\"$data\",$user)";


			if($res->getMoto() || $res->getAttrezzatura()) {
				// va creata anche la entry nella tabella noleggio
				// come andare a prendersi il codice della moto da inserire nella prenotazione ---
			}

			mysqli_query($this->conn, $sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}
	}
?>