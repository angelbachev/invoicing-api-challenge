<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Http;

use App\Application\Command\Invoice\SumInvoices\SumInvoicesCommandResponse;
use App\Application\Shared\ErrorResponse;
use App\Infrastructure\Domain\Model\ConstraintViolation;
use App\Infrastructure\Http\ResponseMapper;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\CustomerFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ResponseMapperTest extends KernelTestCase
{
    private ResponseMapper $responseMapper;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ResponseMapper $responseMapper */
        $responseMapper = self::getContainer()->get(ResponseMapper::class);
        $this->responseMapper = $responseMapper;
    }

    /**
     * @dataProvider provideSuccessfulResponses
     */
    public function testSerializeSuccessfulResponse(mixed $data, mixed $expectedData, int $statusCode): void
    {
        $response = $this->responseMapper->serializeSuccessfulResponse($data, $statusCode);

        $expectedResponse = JsonResponse::fromJsonString((string) json_encode($expectedData), $statusCode);
        $this->assertEquals($expectedResponse, $response);
        $this->assertSame($statusCode, $response->getStatusCode());
    }

    /**
     * @dataProvider provideErrorResponses
     */
    public function testSerializeErrorResponse(ErrorResponse $data, array $expectedData, int $statusCode): void
    {
        $response = $this->responseMapper->serializeErrorResponse($data);
        $expectedResponse = new JsonResponse($expectedData, $statusCode);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @dataProvider provideSuccessfulResponses
     */
    public function testSerializeResponseWithSuccessfulResponse(mixed $data, mixed $expectedData, int $statusCode): void
    {
        $response = $this->responseMapper->serializeResponse($data);

        $expectedResponse = new JsonResponse($expectedData, $statusCode);
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @dataProvider provideErrorResponses
     */
    public function testSerializeResponseWithErrorResponse(ErrorResponse $data, array $expectedData, int $statusCode): void
    {
        $response = $this->responseMapper->serializeResponse($data);
        $expectedResponse = new JsonResponse($expectedData, $statusCode);

        $this->assertEquals($expectedResponse, $response);
    }

    public function provideSuccessfulResponses(): array
    {
        $data = new SumInvoicesCommandResponse(
            CurrencyFactory::BGN_CODE,
            [
                [
                    'name' => CustomerFactory::CUSTOMER_1_NAME,
                    'balance' => CustomerFactory::CUSTOMER_1_BALANCE_BGN,
                ],
                [
                    'name' => CustomerFactory::CUSTOMER_2_NAME,
                    'balance' => CustomerFactory::CUSTOMER_2_BALANCE_BGN,
                ],
            ]
        );

        return [
            'data' => [$data, ['currency' => $data->getCurrency(), 'customers' => $data->getCustomers()], 200],
            'null' => [null, null, 200],
        ];
    }

    public function provideErrorResponses(): array
    {
        return [
            '400' => [ErrorResponse::fromCustomError(new ConstraintViolation('path', 'message')), ['errors' => ['path' => 'message']], 400],
            '404' => [ErrorResponse::notFound(), ['message' => 'Not Found'], 404],
        ];
    }
}
