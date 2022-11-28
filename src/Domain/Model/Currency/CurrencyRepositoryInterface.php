<?php

declare(strict_types=1);

namespace App\Domain\Model\Currency;

interface CurrencyRepositoryInterface
{
    public function findOneByCode(string $code): ?Currency;

    /**
     * @return Currency[]
     */
    public function findAll(): array;

    public function save(Currency $currency): void;
}
