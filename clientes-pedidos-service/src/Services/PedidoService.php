<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pedido;
use App\Repositories\ClienteRepository;
use App\Repositories\PedidoRepository;
use InvalidArgumentException;
use RuntimeException;

class PedidoService
{
    public function __construct(
        private readonly PedidoRepository $pedidoRepository,
        private readonly ClienteRepository $clienteRepository
    ) {}

    public function listar(array $filters, int $page, int $perPage): array
    {
        return $this->pedidoRepository->findAll($filters, $page, $perPage);
    }

    public function obtener(int $id): array
    {
        $pedido = $this->pedidoRepository->findById($id);
        if ($pedido === null) {
            throw new RuntimeException("Pedido no encontrado.", 404);
        }
        return $pedido->toArray();
    }

    public function crear(array $data): array
    {
        $this->validatePedidoData($data);
        return $this->pedidoRepository->create($data)->toArray();
    }

    public function actualizar(int $id, array $data): array
    {
        $this->validatePedidoData($data);
        $pedido = $this->pedidoRepository->update($id, $data);
        if ($pedido === null) {
            throw new RuntimeException("Pedido no encontrado.", 404);
        }
        return $pedido->toArray();
    }

    public function completar(int $id): array
    {
        return $this->cambiarEstado($id, Pedido::ESTADO_COMPLETADO);
    }

    public function cancelar(int $id): array
    {
        return $this->cambiarEstado($id, Pedido::ESTADO_CANCELADO);
    }

    public function eliminar(int $id): void
    {
        if (!$this->pedidoRepository->delete($id)) {
            throw new RuntimeException("Pedido no encontrado.", 404);
        }
    }

    public function estadisticas(): array
    {
        return $this->pedidoRepository->getEstadisticas();
    }

    private function cambiarEstado(int $id, string $estado): array
    {
        $pedido = $this->pedidoRepository->findById($id);
        if ($pedido === null) {
            throw new RuntimeException("Pedido no encontrado.", 404);
        }
        if ($pedido->estado === $estado) {
            throw new InvalidArgumentException("El pedido ya está en estado '{$estado}'.");
        }
        return $this->pedidoRepository->updateEstado($id, $estado)->toArray();
    }

    private function validatePedidoData(array $data): void
    {
        if (empty($data['cliente_id']) || !is_numeric($data['cliente_id'])) {
            throw new InvalidArgumentException("El cliente_id es obligatorio.");
        }
        if ($this->clienteRepository->findById((int)$data['cliente_id']) === null) {
            throw new InvalidArgumentException("El cliente especificado no existe.");
        }
        if (empty($data['descripcion'])) {
            throw new InvalidArgumentException("La descripción es obligatoria.");
        }
        if (!isset($data['total']) || !is_numeric($data['total']) || $data['total'] < 0) {
            throw new InvalidArgumentException("El total debe ser un número positivo.");
        }
        if (!empty($data['estado']) && !in_array($data['estado'], Pedido::ESTADOS_VALIDOS, true)) {
            throw new InvalidArgumentException("Estado inválido. Use: " . implode(', ', Pedido::ESTADOS_VALIDOS));
        }
    }
}
