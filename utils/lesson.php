<?php
namespace LEZIONE;

class Lesson {
    private int $id;
    private string $data;
    private int $posti;
    private string $descrizione;
    private string $istruttore;
    private int $tracciato;

    function __construct(int $id, string $data, string $desc, string $istr, int $track, int $posti) {
        $this->id = $id;
        $this->data = $data;
        $this->descrizione = $desc;
        $this->istruttore = $istr;
        $this->tracciato = $track;
        $this->posti = $posti;
    }

    public function getID(): int {
        return $this->id;
    }

    public function getTrack(): int {
        return $this->tracciato;
    }

    public function getData(): string {
        return $this->data;
    }

    public function getDesc(): string {
        return $this->descrizione;
    }

    public function getPosti(): int {
        return $this->posti;
    }

    public function getIstruttore(): string {
        return $this->istruttore;
    }

    public function setNewId(int $id) {
        $this->id = $id;
    }
}
?>