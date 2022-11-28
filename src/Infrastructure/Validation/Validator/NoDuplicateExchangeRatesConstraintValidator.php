<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class NoDuplicateExchangeRatesConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        $currencyCodes = [];
        foreach ($value as $exchangeRate) {
            [$code, $rate] = explode(':', $exchangeRate);
            if (in_array($code, $currencyCodes, true)) {
                $this->context->buildViolation('Currency code must be unique')
                    ->addViolation();
            }

            $currencyCodes[] = $code;
        }
    }
}
