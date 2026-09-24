<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PedidoService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class PedidoController extends BaseController
{
    public function __construct(private readonly PedidoService $service) {}

    public function index(Request $request, Response $response): Response
    {
        ['page' => $page, 'per_page' => $perPage] = $this->getPaginationParams($request);
        $filters = array_intersect_key(
            $request->getQueryParams(),
            array_flip(['estado', 'cliente_id', 'fecha_desde', 'fecha_hasta'])
        );
        return $this->success($response, $this->service->listar($filters, $page, $perPage));
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
            return $this->success($response, $this->service->crear($data), 'Pedido creado.', 201);
        } catch (InvalidArgumentException $e) {
            return $this->error($response, $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = (array)$request->getParsedBody();
            return $this->success($response, $this->service->actualizar((int)$args['id'], $data), 'Pedido actualizado.');
        } catch (InvalidArgumentException $e) {
            return $this->error($response, $e->getMessage(), 422);
        } catch (RuntimeException $e) {
            return $this->error($response, $e->getMessage(), (int)$e->getCode() ?: 404);
        }
    }

    public function completar(Request $request, Response $response, array $args): Response
    {
        try {
            return $this->success($response, $this->service->completar((int)$args['id']), 'Pedido completado.');
        } catch (RuntimeException | InvalidArgumentException $e) {
            return $this->error($response, $e->getMessage(), (int)$e->getCode() ?: 400);
        }
    }

    public function cancelar(Request $request, Response $response, array $args): Response
    {
        try {
            return $this->success($response, $this->service->cancelar((int)$args['id']), 'Pedido cancelado.');
        } catch (RuntimeException | InvalidArgumentException $e) {
            return $this->error($response, $e->getMessage(), (int)$e->getCode() ?: 400);
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->service->eliminar((int)$args['id']);
            return $this->success($response, null, 'Pedido eliminado.');
        } catch (RuntimeException $e) {
            return $this->error($response, $e->getMessage(), (int)$e->getCode() ?: 404);
        }
    }

    public function estadisticas(Request $request, Response $response): Response
    {
        return $this->success($response, $this->service->estadisticas());
    }
}
