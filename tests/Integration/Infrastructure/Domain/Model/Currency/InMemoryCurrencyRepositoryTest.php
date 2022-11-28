<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Domain\Model\Currency;

use App\Infrastructure\Domain\Model\Currency\InMemoryCurrencyRepository;
use App\Tests\Integration\IntegrationTestCase;
use App\Tests\Shared\Factory\CurrencyFactory;

final class InMemoryCurrencyRepositoryTest extends IntegrationTestCase
{
    private InMemoryCurrencyRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var InMemoryCurrencyRepository $repository */
        $repository = self::getContainer()->get(InMemoryCurrencyRepository::class);
        $this->repository = $repository;
    }

    public function testMethods(): void
    {
        $currency = CurrencyFactory::getCurrency();

        $this->assertCount(0, $this->repository->findAll());
        $this->assertNull($this->repository->findOneByCode($currency->getCode()));

        $this->repository->save($currency);
        $this->assertCount(1, $this->repository->findAll());
        $this->assertSame($currency, $this->repository->findOneByCode($currency->getCode()));

        $this->repository->save($currency);
        $this->assertCount(1, $this->repository->findAll());
        $this->assertSame($currency, $this->repository->findOneByCode($currency->getCode()));

        $currency = CurrencyFactory::getCurrency(CurrencyFactory::GBP_CODE, CurrencyFactory::GBP_RATE);
        $this->repository->save($currency);
        $this->assertCount(2, $this->repository->findAll());
        $this->assertSame($currency, $this->repository->findOneByCode($currency->getCode()));
    }
}
