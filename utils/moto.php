<?php
namespace MOTO;

class DirtBike {
    private int $id;
    private string $marca;
    private string $modello;
    private int $cilindrata;
    private int $anno;

    function __construct(int $id, string $marca, string $modello, int $cilindrata, int $anno) {
        $this->id = $id;
        $this->marca = $marca;
        $this->modello = $modello;
        $this->cilindrata = $cilindrata;
        $this->anno = $anno;
    }

    public function getID(): int {
        return $this->id;
    }

    public function getMarca(): string {
        return $this->marca;
    }

    public function getModello(): string {
        return $this->modello;
    }

    public function getCilindrata(): int {
        return $this->cilindrata;
    }

    public function getAnno(): int {
        return $this->anno;
    }
}
?>