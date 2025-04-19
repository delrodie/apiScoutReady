<?php

namespace App\DTO;

use App\Entity\District;

class DistrictOutput
{
    public ?int $id = null;
    public ?string $nom = null;
    public ?object $region = null;


    public static function mapToOut(District $district): self
    {
        $dto = new self();
        $dto->id = $district->getId();
        $dto->nom = $district->getNom();
        $dto->region = RegionOutput::mapToOut($district->getRegion()) ;

        return $dto;
    }
}