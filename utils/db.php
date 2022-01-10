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

	class dbAccess {
		private const HOST = '127.0.0.1';
		private const NAME = 'acavalie';
		private const USER = 'acavalie';
		private const PSW = 'aexie7Aht6aut3uo';

		public const QUERIES = array(
			array('SELECT * FROM moto',
				'Errore durante il recupero delle informazioni sulle moto.'), //get all dirtbikes

			array('SELECT * FROM moto WHERE numero = _num_',
				'Errore durante il recupero delle informazioni sulla moto.'), //get specific dirtbike

			array('SELECT data, COUNT(*) AS totNoleggi FROM noleggio WHERE data >= CURDATE() GROUP BY data ORDER BY data',
				'Errore durante il recupero delle informazioni sui noleggi'), //get rent infos

			array('SELECT cognome, nome, marca, modello, numero, attrezzatura FROM (noleggio INNER JOIN utente ON noleggio.utente = utente.cf) '
				.'INNER JOIN moto ON noleggio.moto = moto.numero WHERE data = \'_data_\'',
				'Errore durante il recupero delle informazioni sul noleggio per la data scelta'), //get rent details for a specific date

			array('SELECT * FROM pista','Errore durante il recupero delle informazioni dei tracciati'), //get all tracks

			//5
			array('SELECT * FROM pista WHERE id = _id_','Errore durante il recupero delle informazioni sul tracciato'), // get specific track

			array('SELECT data_disponibile.data AS data, posti, COUNT(*) AS occupati FROM ingressi_entrata INNER JOIN data_disponibile ON
				ingressi_entrata.data = data_disponibile.data WHERE data_disponibile.data >= CURDATE() GROUP BY data_disponibile.data,
				posti ORDER BY data_disponibile.data','Errore durante il recupero delle informazioni sugli ingressi'), //get future entries

			array('SELECT * FROM data_disponibile WHERE data >= CURDATE()','Errore durante il recupero delle informazioni sulle date d\'apertura'), //get future open days

			array('SELECT nome, cognome, moto, attrezzatura FROM (ingressi_entrata INNER JOIN utente ON ingressi_entrata.utente = utente.cf)
				LEFT JOIN noleggio ON ingressi_entrata.utente = noleggio.utente AND ingressi_entrata.data = noleggio.data
				WHERE ingressi_entrata.data = \'_data_\'',
				'Errore durante il recupero delle informazioni sulla data d\'apertura selezionata'), //get entries info from open date

			array('SELECT posti FROM data_disponibile WHERE data = \'_data_\'',
				'Errore durante il recupero delle informazioni sulla data d\'apertura selezionata'), //get entry's infos

			//10
			array('SELECT lezione.id AS id, data_disponibile.data AS data, lezione.posti AS posti, COUNT(*) AS occupati FROM
				(ingressi_lezione INNER JOIN lezione ON ingressi_lezione.lezione = lezione.id)
				INNER JOIN data_disponibile ON lezione.data = data_disponibile.data
				WHERE data_disponibile.data >= CURDATE()
				GROUP BY data_disponibile.data, lezione.posti
				ORDER BY data_disponibile.data',
				'Errore durante il recupero delle informazioni sulle lezioni prenotate'), //get booked lessons' info

			array('SELECT * FROM lezione WHERE data >= CURDATE()',
				'Errore durante il recupero delle informazioni sulle lezioni'), //get lessons' info

			array('SELECT nome, cognome, moto, attrezzatura FROM ((ingressi_lezione INNER JOIN utente ON ingressi_lezione.utente = utente.cf)
				INNER JOIN lezione ON ingressi_lezione.lezione = lezione.id)
				LEFT JOIN noleggio ON ingressi_lezione.utente = noleggio.utente AND lezione.data = noleggio.data
				WHERE ingressi_lezione.lezione = \'_lezione_\'',
				'Errore durante il recupero delle informazioni sulle prenotazioni del corso selezionato'), //get booked record info from lesson

			array('SELECT * FROM lezione WHERE id = _lezione_',
				'Errore durante il recupero delle informazioni del corso selezionato'), //get specific lesson info
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
			$sql = "UPDATE moto SET marca = \"".$moto->getMarca()."\", modello = \"".$moto->getModello()."\", ";
			$sql .= "cilindrata = ".$moto->getCilindrata().", anno = ".$moto->getAnno()." WHERE numero = ".$moto->getID()."";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) //mysqli_affected_rows doesn't work if fields are the same as before!
				return true;
			else
				return false;
		}

		public function createDirtBike(DirtBike $moto): int {
			$sql = "INSERT INTO moto (marca,modello,anno,cilindrata) VALUES (\"".$moto->getMarca()."\",\"".$moto->getModello()."\",";
			$sql .= "".$moto->getAnno().",".$moto->getCilindrata().")";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}

		public function deleteDirtBike(int $id) {
			$sql = "DELETE FROM moto WHERE numero = ".$id."";

			mysqli_query($this->conn,$sql);
		}

		/* ***************************** TRACKS MANAGEMENT ************************** */
		public function createTrack(Track $track): int {
			$sql = "INSERT INTO pista (lunghezza,descrizione,terreno,apertura,chiusura) VALUES (".$track->getLun().",";
			$sql .= "\"".$track->getDesc()."\",\"".$track->getTerreno()."\",\"".$track->getApertura()."\",\"".$track->getChiusura()."\")";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}

		public function updateTrack(Track $track): bool {
			$path = $track->getImgPath() != "" ? "\"".$track->getImgPath()."\"" : "NULL";

			$sql = "UPDATE pista SET lunghezza = ".$track->getLun().", descrizione = \"".$track->getDesc()."\", ";
			$sql .= "terreno = \"".$track->getTerreno()."\", apertura = \"".$track->getApertura()."\", ";
			$sql .= "chiusura = \"".$track->getChiusura()."\", foto = ".$path." WHERE id = ".$track->getID()."";

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
			$sql = "DELETE FROM data_disponibile WHERE data = \"".$date."\"";

			mysqli_query($this->conn,$sql);
		}

		public function updateEntry(Entry $entry): bool {
			$sql = "UPDATE data_disponibile SET posti = ".$entry->getPosti()." WHERE data = \"".$entry->getDate()."\"";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) //mysqli_affected_rows doesn't work if fields are the same as before!
				return true;
			else
				return false;
		}

		public function createEntry(Entry $entry): int {
			$sql = "INSERT INTO data_disponibile (data,posti) VALUES (\"".$entry->getDate()."\",".$entry->getPosti().")";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return mysqli_insert_id($this->conn);
			else
				return -1;
		}


				/* ***************************** LESSONS MANAGEMENT ************************** */
				public function deleteLesson(int $id) {
					$sql = "DELETE FROM lezioni WHERE data = $id";

					mysqli_query($this->conn,$sql);
				}

				public function updateLesson(Lesson $lesson): bool {
					$sql = "UPDATE lezione SET data = \"".$lesson->getData()."\", posti = ".$lesson->getPosti().", ";
					$sql .= "descrizione = \"".$lesson->getDesc()."\", istruttore = \"".$lesson->getIstruttore()."\", ";
					$sql .= "pista = ".$lesson->getTrack()." WHERE id = ".$lesson->getID()."";

					mysqli_query($this->conn,$sql);

					if(!mysqli_error($this->conn)) //mysqli_affected_rows doesn't work if fields are the same as before!
						return true;
					else
						return false;
				}

				public function createLesson(Lesson $lesson): int {
					$sql = "INSERT INTO lezione (data,posti,descrizione,istruttore,pista) VALUES (\"".$lesson->getData()."\",";
					$sql .= "".$lesson->getPosti().", \"".$lesson->getDesc()."\", \"".$lesson->getIstruttore()."\",".$lesson->getTrack().")";

					mysqli_query($this->conn,$sql);

					if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
						return mysqli_insert_id($this->conn);
					else
						return -1;
				}
	}
?>