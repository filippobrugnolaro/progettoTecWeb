<?php
	namespace DB;

	require_once('moto.php');
	require_once('user.php');

	use Exception;
	use MOTO\DirtBike;
	use USER\User;

	class dbAccess {
		private const HOST = '127.0.0.1';
		private const NAME = 'acavalie';
		private const USER = 'acavalie';
		private const PSW = 'aexie7Aht6aut3uo';

		public const QUERIES = array(
			array('SELECT * FROM moto','Errore durante il recupero delle informazioni sulle moto.'), //get all dirtbikes
			array('SELECT * FROM moto WHERE numero = _num_','Errore durante il recupero delle informazioni sulla moto.'), //get specific dirtbike
			array('SELECT data, COUNT(*) AS totNoleggi FROM noleggio WHERE data >= CURDATE() GROUP BY data ORDER BY data',
				'Errore durante il recupero delle informazioni sui noleggi'), //get rent infos
			array('SELECT cognome, nome, marca, modello, numero, attrezzatura FROM (noleggio INNER JOIN utente ON noleggio.utente = utente.cf) '
				.'INNER JOIN moto ON noleggio.moto = moto.numero WHERE data = \'_data_\'',
				'Errore durante il recupero delle informazioni sul noleggio per la data scelta'), //get rent details for a specific date
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

		public function updateDirtBike(DirtBike $moto): bool {
			$sql = "UPDATE moto SET marca = \"".$moto->getMarca()."\", modello = \"".$moto->getModello()."\", ";
			$sql .= "cilindrata = ".$moto->getCilindrata().", anno = ".$moto->getAnno()." WHERE numero = ".$moto->getID()."";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn)) //mysqli_affected_rows doesn't work if fields are the same as before!
				return true;
			else
				return false;
		}

		public function createDirtBike(DirtBike $moto): bool {
			$sql = "INSERT INTO moto (marca,modello,anno,cilindrata) VALUES (\"".$moto->getMarca()."\",\"".$moto->getModello()."\",";
			$sql .= "".$moto->getAnno().",".$moto->getCilindrata().")";

			mysqli_query($this->conn,$sql);

			if(!mysqli_error($this->conn) && mysqli_affected_rows($this->conn))
				return true;
			else
				return false;
		}

		public function deleteDirtBike(int $id) {
			$sql = "DELETE FROM moto WHERE numero = ".$id."";

			mysqli_query($this->conn,$sql);
		}

		// public function addCharacter($nome, $col, $peso, $potenza, $ab, $abr, $absw, $abs, $desc) {
		// 	$query = "INSERT INTO personaggi (nome, colore, peso, potenza, descrizione, angry_birds, angry_birds_rio, angry_birds_star_wars, angry_birds_space) VALUES(";
		// 	$query .= "\"$nome\",\"$col\",$peso,\"$potenza\",$ab,$abr,$absw,$abs,\"$desc\")";

		// 	$res = mysqli_query($this->conn,$query) or die(mysqli_error($this->conn));

		// 	if(mysqli_affected_rows())
		// 		return true;
		// 	else
		// 		return false;
		// }

	}
?>