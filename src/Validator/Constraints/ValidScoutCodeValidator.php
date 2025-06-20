<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidScoutCodeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidScoutCode || !is_string($value)){
            return;
        }

        if (!preg_match('/^[A-Z]{2}\d{6}\d{4}-[A-F0-9]{2}$/', $value)){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation();
            return;
        }

        [$base, $checksum] = explode('-', $value);
        $expected = strtoupper(substr(hash('crc32b', $base), 0, 2));

        if ($checksum !== $expected){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation();
        }
    }
}