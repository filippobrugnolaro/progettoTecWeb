<?php
namespace MESSAGGIO;

use DateTime;

class Message {
    private int $id;
    private string $nominativo;
    private string $email;
    private string $tel;
    private string $obj;
    private string $messaggio;
    private ?DateTime $data;

    function __construct(int $id, string $nominativo, string $email, string $tel, string $obj, string $messaggio, ?DateTime $data = null) {
        $this->id = $id;
        $this->nominativo = $nominativo;
        $this->email = $email;
        $this->tel = $tel;
        $this->obj = $obj;
        $this->messaggio = $messaggio;
        $this->data = $data;
    }

    public function getID(): int {
        return $this->id;
    }

    public function getNominativo(): string {
        return $this->nominativo;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getTel(): string {
        return $this->tel;
    }

    public function getObj(): string {
        return $this->obj;
    }

    public function getText(): string {
        return $this->messaggio;
    }

    public function getData(): DateTime {
        return $this->data;
    }
}
?>