<?php

declare(strict_types=1);

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class JwtMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $secret,
        private readonly string $issuer
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorized('Token no proporcionado.');
        }

        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $handler->handle($request->withAttribute('jwt_payload', $decoded));
        } catch (ExpiredException) {
            return $this->unauthorized('Token expirado.');
        } catch (SignatureInvalidException) {
            return $this->unauthorized('Token con firma inválida.');
        } catch (\Exception) {
            return $this->unauthorized('Token inválido.');
        }
    }

    private function unauthorized(string $message): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['success' => false, 'message' => $message, 'data' => null]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
}
