<?php

declare(strict_types=1);

namespace App\Infrastructure\Delivery\Api\V1\Public\Invoice;

use App\Application\Command\Invoice\SumInvoices\SumInvoicesCommand;
use App\Application\Shared\ErrorResponse;
use App\Infrastructure\Http\ResponseMapper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class SumInvoicesAction
{
    use HandleTrait;

    private readonly ResponseMapper $responseMapper;

    public function __construct(
        MessageBusInterface $messageBus,
        ResponseMapper $responseMapper,
    ) {
        $this->messageBus = $messageBus;
        $this->responseMapper = $responseMapper;
    }

    #[Route(path: '/sumInvoices', name: 'sum-invoices', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        $exchangeRates = $request->request->all('exchangeRates');
        $outputCurrency = $request->request->get('outputCurrency');
        $customerVat = $request->request->get('customerVat');
        $command = new SumInvoicesCommand(
            exchangeRates: $exchangeRates,
            outputCurrency: is_string($outputCurrency) ? $outputCurrency : '',
            file: $file instanceof UploadedFile ? $file : null,
            customerVat: is_string($customerVat) ? $customerVat : null,
        );

        /** @var SumInvoicesCommand|ErrorResponse $response */
        $response = $this->handle($command);

        return $this->responseMapper->serializeResponse($response);
    }
}
