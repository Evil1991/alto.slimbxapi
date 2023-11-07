<?php

namespace Alto\Slimbxapi\Core;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Билдер ответов
 */
class ResponseBuilder
{
    /**
     * Успешный ответ в формате JSON
     */
    public static function successJSON(Response $response, array $responseData): Response
    {
        $success = [
            'status'    => true,
            'data'      => $responseData
        ];

        $payload = json_encode($success, JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * Ответ в случае ошибки (ошибок) в формате JSON
     * Данные ошибки берутся из ResponseErrorsService
     */
    public static function errorJson(Response $response, int $statusCode = 400): Response
    {
        $error = [
            'status' => false,
            'errors' => ResponseErrors::getErrors()
        ];

        $payload = json_encode($error, JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);

        return $response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * Ответ в случае ошибки (ошибок) в формате JSON
     * Отличие от предыдущего метода в явном передаче payload-а ошибок в виде массива
     */
    public static function errorJsonRaw(Response $response, array $errors, int $statusCode = 400): Response
    {
        $error = [
            'status' => false,
            'errors' => $errors
        ];

        $payload = json_encode($error, JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);

        return $response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * Успешный ответ в формате text/html
     */
    public static function successHTML(Response $response, string $data): Response
    {
        $response->getBody()->write($data);

        return $response;
    }

    /**
     * Метод текстового ответа в случае ошибки
     */
    public static function errorHTML(Response $response, string $data, int $statusCode = 400): Response
    {
        $response->getBody()->write($data);

        return $response
            ->withStatus($statusCode);
    }
}
