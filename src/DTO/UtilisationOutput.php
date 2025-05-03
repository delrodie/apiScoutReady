<?php

namespace App\DTO;

use App\Entity\Utilisation;
use App\Service\Variables;

class UtilisationOutput
{
    public ?int $id = null;
    public ?object $scout = null;
    public ?object $groupe = null;
    public ?string $statut = null;
    public ?string $demandeur = null;
    public ?string $approbateur = null;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;


    public static function mapToOut(Utilisation $utilisation, string $baseUrl): self
    {
        $dto = new self();
        $dto->id = $utilisation->getId();
        $dto->statut = Variables::statutLibelle((int) $utilisation->getStatut()) ;
        $dto->demandeur = $utilisation->getDemandeur();
        $dto->approbateur = $utilisation->getApprobateur();
        $dto->createdAt = $utilisation->getCreatedAt()?->format('Y-m-d H:i:s');
        $dto->updatedAt = $utilisation->getUpdatedAt()?->format('Y-m-d H:i:s');
        $dto->scout = ScoutOutput::mapToOut($utilisation->getScout(), $baseUrl);
        $dto->groupe = GroupeOutput::mapToOut($utilisation->getGroupe());

        return $dto;
    }
}