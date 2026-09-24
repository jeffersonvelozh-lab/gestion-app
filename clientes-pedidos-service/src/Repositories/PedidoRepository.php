<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;
use App\Models\Pedido;

class PedidoRepository
{
    public function __construct(private readonly Connection $connection) {}

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['estado'])) {
            $where[] = 'p.estado = :estado';
            $params['estado'] = $filters['estado'];
        }
        if (!empty($filters['cliente_id'])) {
            $where[] = 'p.cliente_id = :cliente_id';
            $params['cliente_id'] = (int)$filters['cliente_id'];
        }
        if (!empty($filters['fecha_desde'])) {
            $where[] = 'p.fecha_pedido >= :fecha_desde';
            $params['fecha_desde'] = $filters['fecha_desde'];
        }
        if (!empty($filters['fecha_hasta'])) {
            $where[] = 'p.fecha_pedido <= :fecha_hasta';
            $params['fecha_hasta'] = $filters['fecha_hasta'];
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->connection->getPdo()->prepare(
            "SELECT p.*, c.nombre AS cliente_nombre
             FROM \"Pedidos\" p
             INNER JOIN \"Clientes\" c ON c.id = p.cliente_id
             WHERE {$whereClause}
             ORDER BY p.id
             LIMIT :perPage OFFSET :offset"
        );
        $params['offset'] = $offset;
        $params['perPage'] = $perPage;
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        unset($params['offset'], $params['perPage']);
        $countStmt = $this->connection->getPdo()->prepare(
            "SELECT COUNT(*) FROM \"Pedidos\" p INNER JOIN \"Clientes\" c ON c.id = p.cliente_id WHERE {$whereClause}"
        );
        $countStmt->execute($params);

        return [
            'data'     => array_map(fn($r) => Pedido::fromArray($r)->toArray() + ['cliente_nombre' => $r['cliente_nombre']], $rows),
            'total'    => (int)$countStmt->fetchColumn(),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function findById(int $id): ?Pedido
    {
        $stmt = $this->connection->getPdo()->prepare('SELECT * FROM "Pedidos" WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Pedido::fromArray($row) : null;
    }

    public function create(array $data): Pedido
    {
        $stmt = $this->connection->getPdo()->prepare(
            'INSERT INTO "Pedidos" (cliente_id, descripcion, total, estado, fecha_pedido)
             VALUES (:cliente_id, :descripcion, :total, :estado, :fecha_pedido)
             RETURNING *'
        );
        $stmt->execute([
            'cliente_id'  => $data['cliente_id'],
            'descripcion' => $data['descripcion'],
            'total'       => $data['total'],
            'estado'      => $data['estado'] ?? Pedido::ESTADO_PENDIENTE,
            'fecha_pedido'=> $data['fecha_pedido'] ?? date('Y-m-d'),
        ]);
        return Pedido::fromArray($stmt->fetch());
    }

    public function update(int $id, array $data): ?Pedido
    {
        $stmt = $this->connection->getPdo()->prepare(
            'UPDATE "Pedidos" SET cliente_id = :cliente_id, descripcion = :descripcion,
             total = :total, estado = :estado, fecha_pedido = :fecha_pedido, updated_at = NOW()
             WHERE id = :id
             RETURNING *'
        );
        $stmt->execute([
            'cliente_id'  => $data['cliente_id'],
            'descripcion' => $data['descripcion'],
            'total'       => $data['total'],
            'estado'      => $data['estado'],
            'fecha_pedido'=> $data['fecha_pedido'],
            'id'          => $id,
        ]);
        $row = $stmt->fetch();
        return $row ? Pedido::fromArray($row) : null;
    }

    public function updateEstado(int $id, string $estado): ?Pedido
    {
        $stmt = $this->connection->getPdo()->prepare(
            'UPDATE "Pedidos" SET estado = :estado, updated_at = NOW() WHERE id = :id RETURNING *'
        );
        $stmt->execute(['estado' => $estado, 'id' => $id]);
        $row = $stmt->fetch();
        return $row ? Pedido::fromArray($row) : null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->getPdo()->prepare('DELETE FROM "Pedidos" WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getEstadisticas(): array
    {
        $pdo = $this->connection->getPdo();

        $total       = (int)$pdo->query('SELECT COUNT(*) FROM "Pedidos"')->fetchColumn();
        $completados = (int)$pdo->query("SELECT COUNT(*) FROM \"Pedidos\" WHERE estado = 'completado'")->fetchColumn();
        $pendientes  = (int)$pdo->query("SELECT COUNT(*) FROM \"Pedidos\" WHERE estado = 'pendiente'")->fetchColumn();
        $cancelados  = (int)$pdo->query("SELECT COUNT(*) FROM \"Pedidos\" WHERE estado = 'cancelado'")->fetchColumn();
        $clientesActivos = (int)$pdo->query('SELECT COUNT(*) FROM "Clientes" WHERE activo = TRUE')->fetchColumn();

        $stmt = $pdo->query(
            "SELECT fecha_pedido::date AS fecha, COUNT(*) AS total
             FROM \"Pedidos\"
             WHERE fecha_pedido >= NOW() - INTERVAL '6 months'
             GROUP BY fecha_pedido::date
             ORDER BY fecha"
        );

        return [
            'total_pedidos'    => $total,
            'completados'      => $completados,
            'pendientes'       => $pendientes,
            'cancelados'       => $cancelados,
            'clientes_activos' => $clientesActivos,
            'actividad_por_dia'=> $stmt->fetchAll(),
        ];
    }
}
