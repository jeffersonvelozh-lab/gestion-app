<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;
use App\Models\Cliente;

class ClienteRepository
{
    public function __construct(private readonly Connection $connection) {}

    public function findAll(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->connection->getPdo()->prepare(
            'SELECT * FROM "Clientes" WHERE activo = TRUE ORDER BY id LIMIT :perPage OFFSET :offset'
        );
        $stmt->execute(['offset' => $offset, 'perPage' => $perPage]);
        $rows = $stmt->fetchAll();

        $total = (int)$this->connection->getPdo()->query('SELECT COUNT(*) FROM "Clientes" WHERE activo = TRUE')->fetchColumn();

        return [
            'data'     => array_map(fn($r) => Cliente::fromArray($r)->toArray(), $rows),
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function findById(int $id): ?Cliente
    {
        $stmt = $this->connection->getPdo()->prepare('SELECT * FROM "Clientes" WHERE id = :id AND activo = TRUE');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Cliente::fromArray($row) : null;
    }

    public function create(array $data): Cliente
    {
        $stmt = $this->connection->getPdo()->prepare(
            'INSERT INTO "Clientes" (nombre, email, telefono, direccion, activo)
             VALUES (:nombre, :email, :telefono, :direccion, TRUE)
             RETURNING *'
        );
        $stmt->execute([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'telefono' => $data['telefono'] ?? '',
            'direccion'=> $data['direccion'] ?? '',
        ]);
        return Cliente::fromArray($stmt->fetch());
    }

    public function update(int $id, array $data): ?Cliente
    {
        $stmt = $this->connection->getPdo()->prepare(
            'UPDATE "Clientes" SET nombre = :nombre, email = :email, telefono = :telefono,
             direccion = :direccion, updated_at = NOW()
             WHERE id = :id AND activo = TRUE
             RETURNING *'
        );
        $stmt->execute([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'telefono' => $data['telefono'] ?? '',
            'direccion'=> $data['direccion'] ?? '',
            'id'       => $id,
        ]);
        $row = $stmt->fetch();
        return $row ? Cliente::fromArray($row) : null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->getPdo()->prepare(
            'UPDATE "Clientes" SET activo = FALSE, updated_at = NOW() WHERE id = :id AND activo = TRUE'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
