<?php

namespace Alto\Slimbxapi\Core;

use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;
use Alto\Slimbxapi\Exceptions\LogicException;
use Exception;
use Throwable;

/**
 * Обработка ошибок
 */
class ApiErrorHandler extends ErrorHandler
{
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';

    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;

        if ($exception instanceof HttpException) {
            $resolve = $this->resolveHttpExceptionResponseData($exception);
        } elseif ($exception instanceof LogicException) {
            $resolve = $this->resolveLogicExceptionResponseData($exception);
        } else {
            $resolve = $this->resolveOtherExceptionResponseData($exception);
        }

        $payload = json_encode($resolve['error'], JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($resolve['statusCode']);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    protected function resolveHttpExceptionResponseData($exception): array
    {
        $statusCode = 400;
        $type = self::SERVER_ERROR;

        if ($exception instanceof HttpNotFoundException) {
            $type = self::RESOURCE_NOT_FOUND;
        } elseif ($exception instanceof HttpMethodNotAllowedException) {
            $type = self::NOT_ALLOWED;
        } elseif ($exception instanceof HttpUnauthorizedException) {
            $type = self::UNAUTHENTICATED;
        } elseif ($exception instanceof HttpForbiddenException) {
            $type = self::UNAUTHENTICATED;
        } elseif ($exception instanceof HttpBadRequestException) {
            $type = self::BAD_REQUEST;
        } elseif ($exception instanceof HttpNotImplementedException) {
            $type = self::NOT_IMPLEMENTED;
        }

        $statusCode = $exception->getCode();
        $description = $exception->getMessage();

        $error = [
            'status' => false,
            'errors' => [
                [
                    'code' => $type,
                    'message' => $description,
                ]
            ],
        ];

        return ['statusCode' => $statusCode, 'error' => $error];
    }

    protected function resolveLogicExceptionResponseData(LogicException $exception): array
    {
        $statusCode = $exception->getCode();

        $error = [
            'status' => false,
            'errors' => $exception->getErrors(),
        ];

        return ['statusCode' => $statusCode, 'error' => $error];
    }

    protected function resolveOtherExceptionResponseData($exception): array
    {
        $statusCode = 500;
        $description = 'An internal error has occurred while processing your request.';

        if (($exception instanceof Exception || $exception instanceof Throwable)
            && $this->displayErrorDetails
        ) {
            $description = $exception->getMessage();
        }

        $error = [
            'status' => false,
            'errors' => [
                [
                    'code' => self::SERVER_ERROR,
                    'message' => $description,
                ]
            ],
        ];

        return ['statusCode' => $statusCode, 'error' => $error];
    }
}
