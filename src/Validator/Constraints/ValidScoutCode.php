<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidScoutCode extends Constraint
{
    public string $message = 'Le code "{{ code }}" est invalide ou présente un checksum incorrect.';
}