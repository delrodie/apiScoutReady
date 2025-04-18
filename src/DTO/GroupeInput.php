<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;
class GroupeInput
{
    #[Assert\NotBlank(message: 'Le nom du groupe ne doit pas être vide!')]
    public ?string $paroisse = null;

    #[Assert\NotBlank(message: "Le district est requis")]
    public ?int $district = null;
}