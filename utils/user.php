<?php
namespace USER;

class User {
    private $cf;
    private $nome;
    private $cognome;
    private $nascita;
    private $telefono;
    private $email;
    private $psw;
    private $role; //1 == utente, 2 == admin
    private $imgPath;

    function __construct(string $cf, string $nome, string $cognome, string $nascita, string $telefono, string $email, int $role, string $psw = '') {
        $this->cf = $cf;
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->nascita = $nascita;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->role = $role;
        $this->psw = $psw;
    }

    public function getCF(): string {
        return $this->cf;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getCognome(): string {
        return $this->cognome;
    }

    public function getNascita(): string {
        return $this->nascita;
    }

    public function getTelefono(): string {
        return $this->telefono;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getTipoUtente(): int {
        return $this->role;
    }

    public function setImgPath(string $path) {
        $this->imgPath = $path;
    }

    public function setPsw(string $p) {
        $this->psw = $p
    }

}
?>
