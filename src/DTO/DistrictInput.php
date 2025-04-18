<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DistrictInput
{
    #[Assert\NotBlank(message: "Le nom du district ne doit pas être vide.")]
    public ?string $nom = null;

    #[Assert\NotBlank(message: "La région est réquise.")]
    public ?int $region = null;
}