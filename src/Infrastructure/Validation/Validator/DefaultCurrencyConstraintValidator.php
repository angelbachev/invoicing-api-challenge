<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class DefaultCurrencyConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        $defaultCurrencies = array_filter($value, fn (string $exchangeRate) => 1.0 === (float) substr($exchangeRate, 4));
        if (0 === count($defaultCurrencies)) {
            $this->context->buildViolation('Default currency is not passed')
                ->addViolation();
        }
    }
}
