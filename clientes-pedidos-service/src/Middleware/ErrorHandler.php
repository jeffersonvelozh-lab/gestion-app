<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $status = match(true) {
            $exception instanceof HttpNotFoundException => 404,
            $exception instanceof HttpMethodNotAllowedException => 405,
            $exception instanceof \InvalidArgumentException => 422,
            $exception instanceof \RuntimeException && $exception->getCode() > 0 => $exception->getCode(),
            default => 500,
        };

        $message = $displayErrorDetails
            ? $exception->getMessage()
            : ($status < 500 ? $exception->getMessage() : 'Error interno del servidor.');

        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus((int)$status);
    }
}
