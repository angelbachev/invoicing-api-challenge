<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Shared\ErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class ResponseMapper
{
    private const DEFAULT_CONTENT_TYPE = 'json';

    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function serializeSuccessfulResponse(mixed $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $serializedData = $this->serializer->serialize($data, self::DEFAULT_CONTENT_TYPE);

        return JsonResponse::fromJsonString($serializedData, $statusCode);
    }

    public function serializeErrorResponse(ErrorResponse $errorResponse): JsonResponse
    {
        $errors = $errorResponse->getErrors();
        $code = $errorResponse->getStatusCode();

        if (Response::HTTP_NOT_FOUND === $code) {
            return new JsonResponse(
                ['message' => Response::$statusTexts[Response::HTTP_NOT_FOUND]],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(['errors' => $errors], $code);
    }

    /**
     * @param int $successStatusCode it is used only if the response is successful
     */
    public function serializeResponse(mixed $data, int $successStatusCode = Response::HTTP_OK): JsonResponse
    {
        if (null === $data) {
            return new JsonResponse(null, $successStatusCode);
        }

        if ($data instanceof ErrorResponse) {
            return $this->serializeErrorResponse($data);
        }

        return $this->serializeSuccessfulResponse($data, $successStatusCode);
    }
}
