<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AsnInput
{
    #[Assert\NotBlank(message: "Le nom de l'ASN ne doit pas être vide.")]
    public ?string $nom = null;

    #[Assert\NotBlank(message: "Le sigle ne doit pas être vide.")]
    public ?string $sigle = null;
}