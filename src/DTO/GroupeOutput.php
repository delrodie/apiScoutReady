<?php

namespace App\DTO;

use App\Entity\Groupe;

class GroupeOutput
{
    public ?int $id = null;
    public ?string $paroisse = null;
    public ?object $district = null;
    public ?object $region = null;
    public ?object $asn = null;

    public static function mapToOut(Groupe $groupe): self
    {
        $dto = new self();
        $dto->id = $groupe->getId();
        $dto->paroisse = $groupe->getParoisse();
        $dto->district = $groupe->getDistrict();
        $dto->region = $groupe->getDistrict()->getRegion();
        $dto->asn = $groupe->getDistrict()->getRegion()->getAsn();

        return $dto;
    }
}