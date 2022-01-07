<?php
namespace TRACCIATO;

class Track {
    private int $id;
    private int $lunghezza;
    private string $descrizione;
    private string $terreno;
    private string $apertura;
    private string $chiusura;
    private string $path = "";

    function __construct(int $id, int $lun, string $desc, string $terreno, string $open, string $close) {
        $this->id = $id;
        $this->lunghezza = $lun;
        $this->descrizione = $desc;
        $this->terreno = $terreno;
        $this->apertura = $open;
        $this->chiusura = $close;
    }

    public function getID(): int {
        return $this->id;
    }

    public function getLun(): int {
        return $this->lunghezza;
    }

    public function getDesc(): string {
        return $this->descrizione;
    }

    public function getTerreno(): string {
        return $this->terreno;
    }

    public function getApertura(): string {
        return $this->apertura;
    }

    public function getChiusura(): string {
        return $this->chiusura;
    }

    public function getImgPath(): string {
        return $this->path;
    }

    public function setImgPath(string $path) {
        $this->path = $path;
    }

    public function setNewId(int $id) {
        $this->id = $id;
    }
}
?>