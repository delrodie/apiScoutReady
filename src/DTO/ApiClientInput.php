<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ApiClientInput
{
    #[Assert\NotBlank(message:" Le nom ne peut être vide!")]
    public ?string $name = null;

    #[Assert\NotBlank(message: "Le role ne peut être vide ")]
    public ?array $roles = null;
}