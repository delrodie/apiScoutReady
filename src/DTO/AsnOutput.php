<?php

namespace App\DTO;

use App\Entity\Asn;

class AsnOutput
{
    public ?int $id = null;
    public ?string $nom = null;
    public ?string $sigle = null;

    public static function mapToOutput(Asn $asn): self
    {
        $dto = new self();
        $dto->id = $asn->getId();
        $dto->sigle = $asn->getSigle();
        $dto->nom = $asn->getNom();

        return $dto;
    }
}