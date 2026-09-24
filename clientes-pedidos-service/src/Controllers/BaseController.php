<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

abstract class BaseController
{
    protected function json(Response $response, mixed $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    protected function success(Response $response, mixed $data, string $message = 'OK', int $status = 200): Response
    {
        return $this->json($response, ['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    protected function error(Response $response, string $message, int $status = 400): Response
    {
        return $this->json($response, ['success' => false, 'message' => $message, 'data' => null], $status);
    }

    protected function getPaginationParams(\Psr\Http\Message\ServerRequestInterface $request): array
    {
        $params = $request->getQueryParams();
        return [
            'page' => max(1, (int)($params['page'] ?? 1)),
            'per_page' => min(100, max(1, (int)($params['per_page'] ?? 15))),
        ];
    }
}
