<?php
namespace PRENOTAZIONELEZ;

class LessonReservation {
    private string $cf;
    private int $lesson;
    private bool $motoBool;
    private ?int $moto;
    private bool $attrezzatura;

    function __construct(string $cf, int $lesson, bool $motoBool, ?int $moto, bool $attrezzatura) {
        $this->cf = $cf;
        $this->lesson = $lesson;
        $this->motoBool = $motoBool;
        $this->moto = $moto;
        $this->attrezzatura = $attrezzatura;
    }

    public function getCF(): string {
        return $this->cf;
    }


    public function getLesson(): int {
        return $this->lesson;
    }

    public function getMotoBool(): bool {
        return $this->motoBool;
    }

    public function getMoto(): ?int {
        return $this->moto;
    }

    public function getCilindrata(): string {
        return $this->ccMoto;
    }

    public function getAttrezzatura(): bool {
        return $this->attrezzatura;
    }
}
?>