<?php

declare(strict_types=1);

namespace App\Application\Command;

use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

abstract class AbstractCommand
{
    public function toArray(): array
    {
        $reflectionClass = new ReflectionClass(get_class($this));
        $response = [];
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            /** @var ReflectionNamedType|null $returnType */
            $returnType = $method->getReturnType();

            if (
                $method->isConstructor() ||
                $method->isAbstract() ||
                $method->isStatic() ||
                'toArray' === $methodName ||
                preg_match('/^set[A-Z]/', $methodName) ||
                null === $returnType ||
                'void' === $returnType->getName()
            ) {
                continue;
            }

            if (preg_match('/^get[A-Z]/', $methodName)) {
                $key = lcfirst(substr($methodName, 3));
            } else {
                $key = lcfirst($methodName);
            }
            $response[$key] = $this->$methodName();
        }

        return $response;
    }
}
