<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ClienteService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class ClienteController extends BaseController
{
    public function __construct(private readonly ClienteService $service) {}

    public function index(Request $request, Response $response): Response
    {
        ['page' => $page, 'per_page' => $perPage] = $this->getPaginationParams($request);
        return $this->success($response, $this->service->listar($page, $perPage));
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            return $this->success($response, $this->service->obtener((int)$args['id']));
        } catch (RuntimeException $e) {
            return $this->error($response, $e->getMessage(), (int)$e->getCode() ?: 404);
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = (array)$request->getParsedBody();
            $cliente = $this->service->crear($data);
            return $this->success($response, $cliente, 'Cliente creado.', 201);
        } catch (InvalidArgumentException $e) {
            return $this->error($response, $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = (array)$request->getParsedBody();
            $cliente = $this->service->actualizar((int)$args['id'], $data);
            return $this->success($response, $cliente, 'Cliente actualizado.');
        } catch (InvalidArgumentException $e) {
            return $this->error($response, $e->getMessage(), 422);
        } catch (RuntimeException $e) {
            return $this->error($response, $e->getMessage(), (int)$e->getCode() ?: 404);
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->service->eliminar((int)$args['id']);
            return $this->success($response, null, 'Cliente eliminado.');
        } catch (RuntimeException $e) {
            return $this->error($response, $e->getMessage(), (int)$e->getCode() ?: 404);
        }
    }
}
