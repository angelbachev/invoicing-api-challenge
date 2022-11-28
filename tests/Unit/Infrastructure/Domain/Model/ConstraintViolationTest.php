<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Domain\Model;

use App\Infrastructure\Domain\Model\ConstraintViolation;
use App\Tests\Shared\Factory\ConstraintViolationFactory as Factory;
use App\Tests\Unit\UnitTestCase;

final class ConstraintViolationTest extends UnitTestCase
{
    public function testConstructorAndGetters(): void
    {
        $constraintViolation = new ConstraintViolation(Factory::PATH, Factory::MESSAGE);
        $this->assertSame(Factory::PATH, $constraintViolation->getPath());
        $this->assertSame(Factory::MESSAGE, $constraintViolation->getMessage());
        $this->assertSame(Factory::CODE, $constraintViolation->getCode());
    }

    public function testCode(): void
    {
        $constraintViolation = new ConstraintViolation(Factory::PATH, Factory::MESSAGE, 404);
        $this->assertSame(Factory::PATH, $constraintViolation->getPath());
        $this->assertSame(Factory::MESSAGE, $constraintViolation->getMessage());
        $this->assertSame(404, $constraintViolation->getCode());
    }
}
