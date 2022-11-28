<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Domain\Model;

use App\Infrastructure\Domain\Model\ConstraintViolationList;
use App\Tests\Shared\Factory\ConstraintViolationFactory;
use App\Tests\Unit\UnitTestCase;

final class ConstraintViolationListTest extends UnitTestCase
{
    public function testDefaultConstructorAndGetters(): void
    {
        $constraintViolationList = new ConstraintViolationList();
        $this->assertSame(0, $constraintViolationList->count());
        $this->assertFalse($constraintViolationList->hasErrors());
        $this->assertSame([], $constraintViolationList->getAll());
    }

    public function testConstructorAndGetters(): void
    {
        $errors = [
            ConstraintViolationFactory::getConstraintViolation(),
            ConstraintViolationFactory::getConstraintViolation(),
        ];
        $constraintViolationList = new ConstraintViolationList($errors);
        $this->assertSame(2, $constraintViolationList->count());
        $this->assertTrue($constraintViolationList->hasErrors());
        $this->assertSame($errors, $constraintViolationList->getAll());
    }

    public function testAddErrors(): void
    {
        $constraintViolationList = new ConstraintViolationList();
        $this->assertSame(0, $constraintViolationList->count());
        $this->assertFalse($constraintViolationList->hasErrors());

        $error = ConstraintViolationFactory::getConstraintViolation();
        $constraintViolationList->addError($error);
        $this->assertSame(1, $constraintViolationList->count());
        $this->assertTrue($constraintViolationList->hasErrors());
        $this->assertSame([$error], $constraintViolationList->getAll());
    }
}
