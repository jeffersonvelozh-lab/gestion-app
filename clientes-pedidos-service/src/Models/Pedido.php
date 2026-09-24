<?php

declare(strict_types=1);

namespace App\Models;

class Pedido
{
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_COMPLETADO = 'completado';
    public const ESTADO_CANCELADO = 'cancelado';

    public const ESTADOS_VALIDOS = [self::ESTADO_PENDIENTE, self::ESTADO_COMPLETADO, self::ESTADO_CANCELADO];

    public function __construct(
        public readonly int $id,
        public readonly int $clienteId,
        public readonly string $descripcion,
        public readonly float $total,
        public readonly string $estado,
        public readonly ?string $fechaPedido,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            clienteId: (int)$data['cliente_id'],
            descripcion: $data['descripcion'],
            total: (float)$data['total'],
            estado: $data['estado'],
            fechaPedido: $data['fecha_pedido'] ?? null,
            createdAt: $data['created_at'] ?? '',
            updatedAt: $data['updated_at'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'cliente_id' => $this->clienteId,
            'descripcion' => $this->descripcion,
            'total' => $this->total,
            'estado' => $this->estado,
            'fecha_pedido' => $this->fechaPedido,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
