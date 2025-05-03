<?php

namespace App\DTO;

class UtilisationInput
{
    public ?string $scout = null;
    public ?int $groupe = null;
    public ?int $statut = null;
    public ?string $demandeur = null;
    public ?string $approbateur = null;
}