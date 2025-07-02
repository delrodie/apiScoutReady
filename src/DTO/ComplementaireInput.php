<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ComplementaireInput
{
    public ?bool $formation = null;

    #[Assert\NotBlank(message: "La branche d'origine ne peut être vide")]
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
    public ?int $scout = null;
}