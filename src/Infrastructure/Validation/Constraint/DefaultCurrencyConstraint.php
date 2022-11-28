<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation\Constraint;

use App\Infrastructure\Validation\Validator\DefaultCurrencyConstraintValidator;
use Symfony\Component\Validator\Constraint;

final class DefaultCurrencyConstraint extends Constraint
{
    public function validatedBy(): string
    {
        return DefaultCurrencyConstraintValidator::class;
    }
}
