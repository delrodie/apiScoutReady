<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegionInput
{
    #[Assert\NotBlank(message: "Le nom de région ne doit pas être vide.")]
    public ?string $nom = null;

    public ?string $symbolique = null;

    #[Assert\NotBlank(message: "L'ASN est requis")]
    public ?int $asn = null;
}