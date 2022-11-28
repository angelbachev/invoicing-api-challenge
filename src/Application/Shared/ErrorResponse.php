<?php

declare(strict_types=1);

namespace App\Application\Shared;

use App\Domain\Model\ConstraintViolationInterface;
use App\Domain\Model\ConstraintViolationListInterface;

final class ErrorResponse
{
    public const DEFAULT_STATUS_CODE = 400;

    public const NOT_FOUND_STATUS_CODE = 404;

    private function __construct(
        private readonly ConstraintViolationListInterface|ConstraintViolationInterface|null $errors = null,
        private readonly int $statusCode = self::DEFAULT_STATUS_CODE
    ) {
    }

    public static function fromErrors(
        ConstraintViolationListInterface $errors,
        int $statusCode = self::DEFAULT_STATUS_CODE
    ): ErrorResponse {
        return new self($errors, $statusCode);
    }

    public static function fromCustomError(
        ConstraintViolationInterface $error,
        int $statusCode = self::DEFAULT_STATUS_CODE
    ): ErrorResponse {
        return new self($error, $statusCode);
    }

    public static function notFound(): ErrorResponse
    {
        return new self(statusCode: self::NOT_FOUND_STATUS_CODE);
    }

    /** @return array<string, string> */
    public function getErrors(): array
    {
        if (null === $this->errors) {
            return [];
        }

        if ($this->errors instanceof ConstraintViolationInterface) {
            return [$this->errors->getPath() => $this->errors->getMessage()];
        }

        $errors = [];
        foreach ($this->errors->getAll() as $error) {
            if (!array_key_exists($error->getPath(), $errors)) {
                $errors[$error->getPath()] = $error->getMessage();
            }
        }

        return $errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
