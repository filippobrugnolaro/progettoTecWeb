<?php
namespace DB;

use Exception;
use USER\User;

class dbAccess {
	private const HOST = "127.0.0.1"; //a chiunque rimanga sulla macchina
	private const NAME = "acavalie";
	private const USER = "acavalie";
	private const PSW = "aexie7Aht6aut3uo";

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

	public function search_user(string $email, string $password): User {
		$password = password_hash($password, PASSWORD_DEFAULT);

		$sql = "SELECT cf FROM utente WHERE email = $email AND password = $password";

		$query = mysqli_query($this->conn,$sql);

		if(!mysqli_error($this->conn)) {
			if(mysqli_num_rows($query) > 0) {
				$user = new User();
				mysqli_free_result($query);
				return $user;
			} else return "Nessun utente corrispondente ai campi inseriti";
		} else {
			throw new Exception("Errore nell'autenticazione. Riprovare più tardi.");
		}

	}

	// public function getCharacters() {
	// 	$sql = "SELECT * FROM personaggi ORDER BY id ASC";

	// 	try {
	// 		$query = mysqli_query($this->conn, $sql);

	// 		if(mysqli_num_rows($query) > 0) { //mettere come primo branch di un IF sempre il ramo che + facilmente viene preso (quello + frequente)

	// 		$result = array();

	// 		while($row = mysqli_fetch_assoc($query)) {
	// 			array_push($result, $row);
	// 		}

	// 		mysqli_free_result($query);
	// 		return $result;

	// 		} else return null;
	// 	} catch(Throwable $t) {
	// 		echo "Errore in getCharacters! ".mysqli_error($this->conn).". :(";
	// 		return null;
	// 	}
	// }

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