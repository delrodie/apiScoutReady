<?php

namespace App\DTO;

use App\Entity\Scout;

class ScoutOutput
{
    public ?int $id = null;
    public ?string $code = null;
    public ?string $matricule = null;
    public ?string $nom = null;
    public ?string $prenom = null;
    public ?string $sexe = null;
    public ?string $dateNaissance = null;
    public ?string $lieuNaissance = null;
    public ?string $telephone = null;
    public ?string $email = null;
    public ?string $photo = null;
    public ?string $fonction = null;
    public ?string $branche = null;
    public ?string $statut = null;
    public ?object $groupe = null;
    public ?bool $telephoneParent = null;
    public ?string $qrCode = null;

    public static function mapToOut(Scout $scout): self
    {
        $dto = new self();
        $dto->id = $scout->getId();
        $dto->code = $scout->getCode();
        $dto->matricule = $scout->getMatricule();
        $dto->nom = $scout->getNom();
        $dto->prenom = $scout->getPrenom();
        $dto->sexe = $scout->getSexe();
        $dto->dateNaissance = $scout->getDateNaissance()?->format('Y-m-d');
        $dto->lieuNaissance = $scout->getLieuNaissance();
        $dto->telephone = $scout->getTelephone();
        $dto->email = $scout->getEmail();
        $dto->photo = $scout->getPhoto();
        $dto->fonction = $scout->getFonction();
        $dto->branche = $scout->getBranche();
        $dto->statut = $scout->getStatut();
        $dto->telephoneParent = $scout->isTelephoneParent();
        $dto->qrCode = $scout->getQrCode();
        $dto->groupe = GroupeOutput::mapToOut($scout->getGroupe()) ;

        return $dto;
    }
}