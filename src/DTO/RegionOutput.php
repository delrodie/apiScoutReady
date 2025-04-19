<?php

namespace App\DTO;

use App\Entity\Region;

class RegionOutput
{
    public ?int $id = null;
    public ?string $nom = null;
    public ?string $symbolique = null;
    public ?object $asn = null;

    public static function mapToOut(Region $region): self
    {
        $dto = new self();
        $dto->id = $region->getId();
        $dto->nom = $region->getNom();
        $dto->symbolique = $region->getSymbolique();
        $dto->asn = AsnOutput::mapToOutput($region->getAsn());

        return $dto;
    }
}