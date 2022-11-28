<?php

declare(strict_types=1);

namespace App\Domain\Model\Currency;

use App\Domain\Model\EntityInterface;

final class Currency implements EntityInterface
{
    public function __construct(private readonly string $code, private readonly float $rate)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function isDefault(): bool
    {
        return 1.0 === $this->rate;
    }

    public function convertTo(Currency $currency): float
    {
        if ($currency->getRate() === $this->getRate()) {
            return 1.0;
        }

        return $currency->getRate() / $this->getRate();
    }
}
