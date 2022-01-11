<?php
namespace PRENOTAZIONE;

class Reservation {
    private string $data;
    private bool $moto;
    private string $ccMoto;
    private bool $attrezzatura;
    private string $taglia;

    function __construct(string $data, bool $moto, string $ccMoto, bool $attrezzatura, string $taglia) {
        $this->data = $data;
        $this->moto = $moto;
        $this->ccMoto = $ccMoto;
        $this->attrezzatura = $attrezzatura;
        $this->taglia = $taglia;
    }

    public function getData(): string {
        return $this->data;
    }

    public function getMoto(): bool {
        return $this->moto;
    }

    public function getCilindrata(): string {
        return $this->ccMoto;
    }

    public function getAttrezzatura(): bool {
        return $this->attrezzatura;
    }

    public function getTaglia(): string {
        return $this->taglia;
    }
}
?>