<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FunctionalTestCase extends WebTestCase
{
    use PHPMatcherAssertions;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    protected function getDecodedJsonResponse(): array
    {
        /** @var string $responseContent */
        $responseContent = $this->client->getResponse()->getContent();
        /** @var array $response */
        $response = json_decode($responseContent, true);

        return $response;
    }
}
