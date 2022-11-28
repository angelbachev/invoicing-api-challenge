<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation\Constraint;

use App\Infrastructure\Validation\Validator\NoDuplicateExchangeRatesConstraintValidator;
use Symfony\Component\Validator\Constraint;

final class NoDuplicateExchangeRatesConstraint extends Constraint
{
    public function validatedBy(): string
    {
        return NoDuplicateExchangeRatesConstraintValidator::class;
    }
}
