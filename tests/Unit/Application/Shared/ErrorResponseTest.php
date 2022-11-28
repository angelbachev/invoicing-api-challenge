<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Shared;

use App\Application\Shared\ErrorResponse;
use App\Infrastructure\Domain\Model\ConstraintViolationList;
use App\Tests\Shared\Factory\ConstraintViolationFactory;
use App\Tests\Unit\UnitTestCase;

final class ErrorResponseTest extends UnitTestCase
{
    public function testFromCustomError(): void
    {
        $error = ConstraintViolationFactory::getConstraintViolation();
        $errorResponse = ErrorResponse::fromCustomError($error, 400);

        $this->assertSame(400, $errorResponse->getStatusCode());
        $this->assertSame([ConstraintViolationFactory::PATH => ConstraintViolationFactory::MESSAGE], $errorResponse->getErrors());
    }

    public function testNotFound(): void
    {
        $errorResponse = ErrorResponse::notFound();

        $this->assertSame(404, $errorResponse->getStatusCode());
        $this->assertEmpty($errorResponse->getErrors());
    }

    public function testFromErrors(): void
    {
        $errors = new ConstraintViolationList();
        $errors->addError(ConstraintViolationFactory::getConstraintViolation());
        $errorResponse = ErrorResponse::fromErrors($errors);

        $this->assertSame(400, $errorResponse->getStatusCode());
        $this->assertSame([ConstraintViolationFactory::PATH => ConstraintViolationFactory::MESSAGE], $errorResponse->getErrors());
    }
}
