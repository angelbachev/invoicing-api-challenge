<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class IntegrationTestCase extends KernelTestCase
{
    use PHPMatcherAssertions;

    protected function setUp(): void
    {
        self::bootKernel();
    }
}
