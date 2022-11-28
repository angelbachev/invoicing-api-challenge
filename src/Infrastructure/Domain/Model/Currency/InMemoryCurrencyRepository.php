<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Model\Currency;

use App\Domain\Model\Currency\Currency;
use App\Domain\Model\Currency\CurrencyRepositoryInterface;
use App\Infrastructure\Persistence\EntityManagerInterface;
use App\Infrastructure\Persistence\EntityRepositoryInterface;

final class InMemoryCurrencyRepository implements CurrencyRepositoryInterface
{
    private readonly EntityManagerInterface $entityManager;
    private readonly EntityRepositoryInterface $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Currency::class);
    }

    public function findOneByCode(string $code): ?Currency
    {
        /** @var ?Currency $currency */
        $currency = $this->repository->find($code);

        return $currency;
    }

    /**
     * @return Currency[]
     */
    public function findAll(): array
    {
        /** @var Currency[] $currencies */
        $currencies = $this->repository->findAll();

        return $currencies;
    }

    public function save(Currency $currency): void
    {
        $this->entityManager->persist($currency);
    }
}
