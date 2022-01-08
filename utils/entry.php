<?php
namespace INGRESSO;

class Entry {
    private string $date;
    private int $posti;

    function __construct(string $date, int $posti) {
        $this->date = $date;
        $this->posti = $posti;
    }

    public function getDate(): string {
        return $this->date;
    }

    public function getPosti(): int {
        return $this->posti;
    }
}
?>