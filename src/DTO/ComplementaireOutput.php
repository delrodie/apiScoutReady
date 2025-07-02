<?php

namespace App\DTO;

use App\Entity\Complementaire;

class ComplementaireOutput
{
    public ?int $id = null;
    public ?bool $formation = null;
    public ?string $brancheOrigine = null;
    public ?string $baseNiveau1 = null;
    public ?int $anneeBaseNiveau1 = null;
    public ?string $baseNiveau2 = null;
    public ?int $anneeBaseNiveau2 = null;
    public ?string $avanceNiveau1 = null;
    public ?int $anneeAvanceNiveau1 = null;
    public ?string $avanceNiveau2 = null;
    public ?int $anneeAvanceNiveau2 = null;
    public ?string $avanceNiveau3 = null;
    public ?int $anneeAvanceNiveau3 = null;
    public ?string $avanceNiveau4 = null;
    public ?int $anneeAvanceNiveau4 = null;
    public ?object $scout = null;

    public static function mapToOut(Complementaire $complementaire): self
    {
        $dto = new self();
        $dto->id = $complementaire->getId();
        $dto->formation = $complementaire->isFormation();
        $dto->brancheOrigine = $complementaire->getBrancheOrigine();
        $dto->baseNiveau1 = $complementaire->getBaseNiveau1();
        $dto->anneeBaseNiveau1 = $complementaire->getAnneeBaseNiveau1();
        $dto->baseNiveau2 = $complementaire->getBaseNiveau2();
        $dto->anneeBaseNiveau2 = $complementaire->getAnneeBaseNiveau2();
        $dto->avanceNiveau1 = $complementaire->getAvanceNiveau1();
        $dto->anneeAvanceNiveau1 = $complementaire->getAnneeAvanceNiveau1();
        $dto->avanceNiveau2 = $complementaire->getAvanceNiveau2();
        $dto->anneeAvanceNiveau2 = $complementaire->getAnneeAvanceNiveau2();
        $dto->avanceNiveau3 = $complementaire->getAvanceNiveau3();
        $dto->anneeAvanceNiveau3 = $complementaire->getAnneeAvanceNiveau3();
        $dto->avanceNiveau4 = $complementaire->getAvanceNiveau4();
        $dto->anneeAvanceNiveau4 = $complementaire->getAnneeAvanceNiveau4();
        $dto->scout = $complementaire->getScout();

        return $dto;
    }
}