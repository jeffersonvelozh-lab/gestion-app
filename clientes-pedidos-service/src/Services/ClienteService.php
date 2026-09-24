<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ClienteRepository;
use InvalidArgumentException;
use RuntimeException;

class ClienteService
{
    public function __construct(private readonly ClienteRepository $repository) {}

    public function listar(int $page, int $perPage): array
    {
        return $this->repository->findAll($page, $perPage);
    }

    public function obtener(int $id): array
    {
        $cliente = $this->repository->findById($id);
        if ($cliente === null) {
            throw new RuntimeException("Cliente no encontrado.", 404);
        }
        return $cliente->toArray();
    }

    public function crear(array $data): array
    {
        $this->validateClienteData($data);
        return $this->repository->create($data)->toArray();
    }

    public function actualizar(int $id, array $data): array
    {
        $this->validateClienteData($data, $id);
        $cliente = $this->repository->update($id, $data);
        if ($cliente === null) {
            throw new RuntimeException("Cliente no encontrado.", 404);
        }
        return $cliente->toArray();
    }

    public function eliminar(int $id): void
    {
        if (!$this->repository->delete($id)) {
            throw new RuntimeException("Cliente no encontrado.", 404);
        }
    }

    private function validateClienteData(array $data, ?int $excludeId = null): void
    {
        if (empty($data['nombre']) || strlen($data['nombre']) > 100) {
            throw new InvalidArgumentException("El nombre es obligatorio y no puede superar 100 caracteres.");
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El email es inválido.");
        }
    }
}
